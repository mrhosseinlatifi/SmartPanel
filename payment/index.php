<?php
define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/config.php';
require_once ROOTPATH . "/bot_file/function/function_tel.php";
require_once ROOTPATH . '/include/db.php';
require_once ROOTPATH . "/media/index.php";
require_once ROOTPATH . "/include/hkbot.php";
require_once ROOTPATH . "/include/domin.php";

/**
 * محاسبه کارمزد درگاه پرداخت
 * @param float $amount مبلغ اصلی
 * @param float $percent_fee درصد کارمزد
 * @param float $max_fee حداکثر کارمزد (0 = بدون محدودیت)
 * @return float کارمزد محاسبه شده
 */
function calculateGatewayCommission($amount, $percent_fee, $max_fee = 0)
{
  if ($percent_fee <= 0) {
    return 0;
  }

  $commission = ($amount * $percent_fee) / 100;

  if ($max_fee > 0 && $commission > $max_fee) {
    $commission = $max_fee;
  }

  return $commission;
}

function markPaymentAsSuccessful($transactionId, $trackingCode, $paymentGateway)
{
    global $db;

    $transactionId = (int) $transactionId;
    $trackingCode = trim((string) $trackingCode);
    $paymentGateway = trim((string) $paymentGateway);

    // Validate input
    if (
        $transactionId <= 0 ||
        $trackingCode === '' ||
        $trackingCode === '0' ||
        $paymentGateway === ''
    ) {
        return false;
    }

    // Unique MySQL advisory lock for this payment
    $lockName = 'payment_tracking_' . hash(
        'sha256',
        $paymentGateway . ':' . $trackingCode
    );

    /*
     * Acquire lock
     */
    $lockStatement = null;

    try {
        $lockStatement = $db->query(
            'SELECT GET_LOCK(:lock_name, 5)',
            [
                ':lock_name' => $lockName
            ]
        );

        if (!$lockStatement) {
            return false;
        }

        $lockResult = $lockStatement->fetchColumn();

        // Important: completely close the result before another query
        $lockStatement->closeCursor();
        $lockStatement = null;

        if ((int) $lockResult !== 1) {
            return false;
        }

        /*
         * Check whether this tracking code is already
         * registered for another payment transaction.
         */
        $alreadyUsed = $db->has('transactions', [
            'tracking_code' => $trackingCode,
            'type' => 'payment',
            'getway' => $paymentGateway,
            'id[!]' => $transactionId,
        ]);

        if ($alreadyUsed) {

            /*
             * Another transaction already owns this tracking code.
             * Reset current pending transaction.
             */
            $db->update(
                'transactions',
                [
                    'status' => 0
                ],
                [
                    'id' => $transactionId,
                    'type' => 'payment',
                    'status' => 3,
                ]
            );

            return false;
        }

        /*
         * Mark current transaction as successful.
         *
         * status 3 = pending
         * status 1 = successful
         */
        $statement = $db->update(
            'transactions',
            [
                'status' => 1,
                'tracking_code' => $trackingCode,
                'getway' => $paymentGateway,
                'type' => 'payment',
            ],
            [
                'id' => $transactionId,
                'status' => 3,
            ]
        );

        return $statement && $statement->rowCount() > 0;

    } finally {

        /*
         * Release MySQL advisory lock.
         *
         * The result must also be consumed/closed,
         * otherwise the same unbuffered-query problem can happen.
         */
        try {
            $releaseStatement = $db->query(
                'SELECT RELEASE_LOCK(:lock_name)',
                [
                    ':lock_name' => $lockName
                ]
            );

            if ($releaseStatement) {
                $releaseStatement->fetchColumn();
                $releaseStatement->closeCursor();
            }
        } catch (Throwable $e) {
            /*
             * Do not replace the original payment exception
             * with a RELEASE_LOCK exception.
             */
        }
    }
}

$bot = new hkbot(Token);
$getBotInfo = $bot->bot('getMe');
$numberId = $getBotInfo['result']['id'];
$idbot = $getBotInfo['result']['username'];

@$section_status = json_decode(get_option('section_status'), 1);
@$media = new media;
if (isset($_GET['file']) && is_string($_GET['file']) && preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['file'])) {
  $gatewayFile = $_GET['file'];
  $allowedPaymentFiles = [
    'aqayepardakht',
    'bitpay',
    'cryptomus',
    'heleket',
    'nowpayments',
    'zarinpal',
    'zibal',
  ];
  if (!in_array($gatewayFile, $allowedPaymentFiles, true)) {
    http_response_code(404);
    exit;
  }
  $result_payment = $db->get('payment_gateways', '*', ['file' => $gatewayFile]);
  if ($result_payment) {
    $settings = [];
    get_settings($settings);
    $ttl = (int) get_option('payment_link_ttl', 3600);
    $ip = getip() ?? 0;
    if ($result_payment['ip']) {
      $country = strtolower(ip_info($ip));
    } else {
      $country = 'iran';
    }

    if ($section_status['main']['payment'] && $result_payment['status']) {
      if ($country == 'iran') {
        if (isset($_GET['code']) && ctype_digit((string) $_GET['code'])) {
          if (isset($_GET['action']) && $_GET['action']) {
            switch ($_GET['action']) {
              case 'get':
                $code = $_GET['code'];
                $payment = $db->get('transactions', '*', ['id' => $code, 'type' => 'payment', 'status' => [2, 3]]);
                if (!$payment) {
                  echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  exit;
                }
                $fid = $payment['user_id'];
                $user = $db->get('users_information', '*', ['user_id' => $fid]);
                $original_amount = $payment['amount'];
                $number = $user['number'];
                $step = $payment['status'];
                $date = $payment['date'];
                $name = $bot->getChatMember($fid)['user']['first_name'];
                $decode_data = json_decode($payment['data'] ?? '', true);
                $decode_data = is_array($decode_data) ? $decode_data : [];

                $gateway_data = json_decode($result_payment['data'] ?? '', true);
                $gateway_data = is_array($gateway_data) ? $gateway_data : [];
                $percent_fee = isset($gateway_data['percent_fee']) ? floatval($gateway_data['percent_fee']) : 0;
                $max_fee = isset($gateway_data['max_fee']) ? floatval($gateway_data['max_fee']) : 0;
                $commission = calculateGatewayCommission($original_amount, $percent_fee, $max_fee);
                $amount = $original_amount + $commission;
                $decode_data['charged_amount'] = $amount;

                if ($date + $ttl >= time()) {

                  if (!$user['block']) {
                    $type = 'get';
                    include ROOTPATH . '/payment/' . $result_payment['file'] . '.php';
                  } else {
                    echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  }
                } else {
                  $db->update('transactions', ['status' => 0], ['id' => $code, 'type' => 'payment', 'status' => [2, 3]]);
                  echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                }

                break;
              case 'back':
                $code = $_GET['code'];
                $tracking_code = null;
                $card = null;
                $payment = $db->get('transactions', '*', ['id' => $code, 'type' => 'payment', 'status' => [1, 3]]);
                if (!$payment) {
                  echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  exit;
                }
                if ($payment['status'] == 1) {
                  header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $payment['tracking_code'] . '&idbot=' . $idbot);
                  exit;
                }
                $fid = $payment['user_id'];
                $user = $db->get('users_information', '*', ['user_id' => $fid]);
                $original_amount = $payment['amount'];
                $number = $user['number'];
                $step = $payment['status'];
                $date = $payment['date'];
                $name = $bot->getChatMember($fid)['user']['first_name'];
                $decode_data = json_decode($payment['data'] ?? '', true);
                $decode_data = is_array($decode_data) ? $decode_data : [];

                $gateway_data = json_decode($result_payment['data'] ?? '', true);
                $gateway_data = is_array($gateway_data) ? $gateway_data : [];
                $percent_fee = isset($gateway_data['percent_fee']) ? floatval($gateway_data['percent_fee']) : 0;
                $max_fee = isset($gateway_data['max_fee']) ? floatval($gateway_data['max_fee']) : 0;
                $commission = calculateGatewayCommission($original_amount, $percent_fee, $max_fee);
                $amount = $decode_data['charged_amount'] ?? ($original_amount + $commission);

                if ($date + $ttl >= time()) {
                  if (!$user['block']) {
                    $type = 'back';
                    $result_ok = false;
                    $result_ipn = false;
                    if ($payment['getway'] == $result_payment['file']) {
                      include ROOTPATH . '/payment/' . $result_payment['file'] . '.php';
                      if ($result_ok) {
                        if ($user["referral_id"] > 0 && !text_contains($user["referral_id"], 'off') && $section_status['main']['free'] && $section_status['free']['gift_payment'] && $fid != $user["referral_id"]) {
                          $gifi = (($original_amount * $settings['gift_payment']) / 100);

                          $usResult = $db->get('users_information', '*', ['user_id' => $user["referral_id"]]);
                          $old_balance = $usResult['balance'];
                          $new_balance = $old_balance + $gifi;
                          insertTransaction('gift', $user["referral_id"], $old_balance, $new_balance, $gifi, 'GiftPayment');

                          $db->update('users_information', ['gift[+]' => $gifi, 'gift_payment[+]' => $gifi], ['user_id' => $user["referral_id"]]);
                          $bot->sm($user["referral_id"], $media->text('refral_gift_payment', [$fid, $name, $original_amount, $gifi]));
                        }
                        if (isset($decode_data['discount'])) {
                          $getDiscount = $db->get('gift_code', '*', ['status' => 1, 'code' => $decode_data['discount']]);

                          if ($getDiscount) {
                            $ids = json_decode($getDiscount['ids'], true) ?: [];

                            if (!in_array($fid, $ids)) {
                              $claim = $db->update('gift_code', ['count[-]' => 1], [
                                'id' => $getDiscount['id'],
                                'status' => 1,
                                'count[>]' => 0,
                              ]);

                              if ($claim && $claim->rowCount() > 0) {
                                $fresh = $db->get('gift_code', '*', ['id' => $getDiscount['id']]);
                                $freshIds = json_decode($fresh['ids'], true) ?: [];

                                if (in_array($fid, $freshIds)) {
                                  $db->update('gift_code', ['count[+]' => 1], ['id' => $getDiscount['id']]);
                                } else {
                                  $decode = json_decode($getDiscount['amount'], true);

                                  $amountDis = ($original_amount * ($decode['amount'] / 100));
                                  if ($amountDis >= $decode['max']) {
                                    $amountDis = $decode['max'];
                                  }
                                  $original_amount += $amountDis;

                                  $freshIds[] = $fid;
                                  if ($fresh['count'] <= 0) {
                                    $db->update('gift_code', ['status' => 0, 'ids[JSON]' => $freshIds], ['id' => $getDiscount['id']]);
                                  } else {
                                    $db->update('gift_code', ['ids[JSON]' => $freshIds], ['id' => $getDiscount['id']]);
                                  }
                                }
                              }
                            }
                          }
                        }
                        $new = $user['balance'] + $original_amount;

                        $db->update('users_information', ['balance[+]' => $original_amount, 'amount_paid[+]' => $original_amount], ['user_id' => $fid]);

                        sm_user(['ok_payment', $tracking_code, $original_amount, $user['balance'], $result_payment['name'], $settings['channel_main']], null, $fid);
                        $payment['tracking_code'] = $tracking_code;
                        sm_channel('channel_transaction', ['admin_ok_payment', $result_payment['file'], $name, $user, $payment, $card]);
                        header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $tracking_code . '&idbot=' . $idbot);
                      } else {
                        if ($result_ipn) {
                          echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('payment_pending', $result_payment['file']) . "</h1>";
                        } else {
                          $db->update('transactions', ['status' => 0], ['id' => $code, 'type' => 'payment', 'status' => 3]);

                          $bot->sm($fid, $media->text('cancel_payment', $result_payment['file']));
                          $base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot . '&msg=' . $media->text('error', $paymentEn);
                          redirect($base_url);
                        }
                      }
                    }
                  } else {
                    echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  }
                } else {
                  $db->update('transactions', ['status' => 0], ['id' => $code, 'type' => 'payment', 'status' => 3]);
                  echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                }

                break;
            }
          }
        }
      } else {
        echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('iran_ip', $result_payment['file']) . "</h1>";
      }
    } else {
      echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('off_dargah', $result_payment['file']) . "</h1>";
    }
  }
}
