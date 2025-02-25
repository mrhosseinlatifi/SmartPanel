<?php
define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/config.php';
require_once ROOTPATH . "/bot_file/function/function.php";
require_once ROOTPATH . '/include/db.php';
require_once ROOTPATH . "/media/index.php";
require_once ROOTPATH . "/include/hkbot.php";
require_once ROOTPATH . "/include/domin.php";

$bot = new hkbot(Token);
$getBotInfo = $bot->bot('getMe');
$numberId = $getBotInfo['result']['id'];
$idbot = $getBotInfo['result']['username'];

@$section_status = json_decode(get_option('section_status'), 1);
@$media = new media;
if (isset($_GET['file'])) {
  $result_payment = $db->get('payment_gateways', '*', ['file' => $_GET['file']]);
  if ($result_payment) {
    get_settings($settings);
    $ip = getip() ?? 0;
    if ($result_payment['ip']) {
      $country = strtolower(ip_info($ip));
    } else {
      $country = 'iran';
    }

    if ($section_status['main']['payment'] && $result_payment['status']) {
      if ($country == 'iran') {
        if (isset($_GET['code'])) {
          if (isset($_GET['action']) && $_GET['action']) {
            switch ($_GET['action']) {
              case 'get':
                $code = $_GET['code'];
                // result transactions
                $payment = $db->get('transactions', '*', ['id' => $code]);
                $fid = $payment['user_id'];
                $user = $db->get('users_information', '*', ['user_id' => $fid]);
                $amount = $payment['amount'];
                $number = $user['number'];
                $step = $payment['status'];
                $date = $payment['date'];
                $name = $bot->getChatMember($fid)['user']['first_name'];
                $decode_data = json_decode($payment['data'],true);

                if ($date <= time() + 3600) {

                  if (!$user['block']) {
                    $type = 'get';
                    include ROOTPATH . '/payment/' . $result_payment['file'] . '.php';
                  } else {
                    echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  }
                } else {

                  echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                }

                break;
              case 'back':
                $code = $_GET['code'];
                // result transactions
                $payment = $db->get('transactions', '*', ['id' => $code]);
                $fid = $payment['user_id'];
                $user = $db->get('users_information', '*', ['user_id' => $fid]);
                $amount = $payment['amount'];
                $number = $user['number'];
                $step = $payment['status'];
                $date = $payment['date'];
                $name = $bot->getChatMember($fid)['user']['first_name'];
                $decode_data = json_decode($payment['data'],true);

                if ($date <= time() + 3600) {

                  if (!$user['block']) {
                    $type = 'back';
                    $result_ok = false;
                    if ($payment['getway'] == $result_payment['file']) {
                      include ROOTPATH . '/payment/' . $result_payment['file'] . '.php';

                      if ($result_ok) {
                        // up ref
                        if ($user["referral_id"] > 0 && !text_contains($user["referral_id"], 'off') && $section_status['main']['free'] && $section_status['free']['gift_payment']) {
                          $gifi = (($amount * $gift_payment) / 100);

                          $usResult = $db->get('users_information', '*', ['user_id' => $user["referral_id"]]);
                          $old_balance = $usResult['balance'];
                          $new_balance = $old_balance + $gifi;
                          insertTransaction('gift', $user["referral_id"], $old_balance, $new_balance, $gifi, 'GiftPayment');

                          $db->update('users_information', ['gift[+]' => $gifi, 'gift_payment[+]' => $gifi], ['user_id' => $user["referral_id"]]);
                          $bot->sm($user["referral_id"], $media->text('refral_gift_payment', [$fid, $name, $amount, $gifi]));
                        }
                        // sm
                        if (isset($decode_data['discount'])) {
                          $getDiscount = $db->get('gift_code', '*', ['status' => 1, 'code' => $decode_data['discount']]);
                          $ids = json_decode($getDiscount['ids'], true);

                          if (!in_array($fid, $ids)) {
                            $decode = json_decode($getDiscount['amount'], true);

                            $amountDis = ($amount * ($decode['amount'] / 100));
                            if ($amountDis >= $decode['max']) {
                              $amountDis = $decode['max'];
                            }
                            $amount += $amountDis;
                            $ids[] = $fid;
                            if (($getDiscount['count'] - 1) == 0) {
                              $db->update('gift_code', ['status' => 0, 'count' => 0, 'ids[JSON]' => $ids], ['id' => $getDiscount['id']]);
                            } else {
                              $db->update('gift_code', ['count[-]' => 1, 'ids[JSON]' => $ids], ['id' => $getDiscount['id']]);
                            }
                          }
                        }
                        $new = $user['balance'] + $amount;

                        //up coin
                        $db->update('users_information', ['balance[+]' => $amount, 'amount_paid[+]' => $amount], ['user_id' => $fid]);

                        sm_user(['ok_payment', $tracking_code, $amount, $user['balance'], $result_payment['name'], $settings['channel_main']], null, $fid);
                        $payment['tracking_code'] = $tracking_code;
                        sm_channel('channel_transaction', ['admin_ok_payment', $result_payment['file'], $name, $user, $payment, $card]);
                        // link
                        header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $tracking_code . '&idbot=' . $idbot);
                      } else {
                        $db->update('transactions', ['status' => 0], ['id' => $code]);

                        $bot->sm($fid, $media->text('cancel_payment', $result_payment['file']));
                        $base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot . '&msg=' . $media->text('error', $paymentEn);
                        redirect($base_url);
                      }
                    } else {
                      echo 1;
                    }
                  } else {
                    echo "<title>@$idbot</title><h1 style='text-align: center;margin-top:30px'>" . $media->text('time_payment_end', $result_payment['file']) . "</h1>";
                  }
                } else {

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
