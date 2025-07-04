<?php

// define('BITPAY_REQUEST_URL', 'https://bitpay.ir/payment/gateway-send');         // Request payment token
// define('BITPAY_VERIFY_URL', 'https://bitpay.ir/payment/gateway-result-second'); // Verify payment
// define('BITPAY_PAYMENT_URL', 'https://bitpay.ir/payment/gateway-NUMBER-get');   // Redirect to payment page
define('BITPAY_REQUEST_URL', 'https://bitpay.ir/payment-test/gateway-send');         // Request payment token
define('BITPAY_VERIFY_URL', 'https://bitpay.ir/payment-test/gateway-result-second'); // Verify payment
define('BITPAY_PAYMENT_URL', 'https://bitpay.ir/payment-test/gateway-NUMBER-get');   // Redirect to payment page

$paymentEn = 'bitpay';
$paymentFa = 'بیت‌پی';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

if ($type === 'get') {
    switch ($step) {
        case 2:
            $url = BITPAY_REQUEST_URL;

            $data_transaction = [
                "api"         => $result_payment['code'],
                "amount"      => $amount * 10,
                "redirect"    => 'https://' . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                "factorId"    => $code,
                "name"        => $name,
                "description" => $media->text(['desc_payment', $fid, $name, $result_payment['name']])
            ];

            $result = sendCurlRequest($url, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors', ['error_getway_get', $paymentEn, $result['error']]);
                redirect($base_url . '&msg=' . $media->text('error', $paymentEn));
            } else {
                if (is_numeric($result['response']) && $result['response'] > 0) {
                    $tracking_code = $result['response'];
                    $db->update('transactions', [
                        'status' => 3,
                        'tracking_code' => $tracking_code,
                        'getway' => $paymentEn,
                        'data[JSON]' => ['ip' => getip(), 'payment' => $paymentEn]
                    ], ['id' => $code]);

                    $url = str_replace('NUMBER', $tracking_code, BITPAY_PAYMENT_URL);
                    header('Location: ' . $url);
                    exit;
                } else {
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, json_encode($result['response'])]);
                    redirect($base_url . '&msg=' . $media->text('error', $paymentEn));
                }
            }
            break;

        case 3:
            if ($payment['getway'] === $paymentEn) {
                $url = str_replace('NUMBER', $tracking_code, BITPAY_PAYMENT_URL);
                header('Location: ' . $url);
                exit;
            } else {
                redirect($base_url . '&msg=' . $media->text('error_getway', $paymentEn));
            }
            break;

        default:
            redirect($base_url);
            break;
    }
} elseif ($type === 'back') {
    if ($payment['tracking_code'] !== $_REQUEST['id_get']) {
        redirect($base_url . '&msg=' . $media->text('error_tracking'));
    }

    $data_transaction = [
        "api"      => $result_payment['code'],
        "id_get"   => $_REQUEST['id_get'],
        "trans_id" => $_REQUEST['trans_id'],
        "json"     => 1
    ];

    $result = sendCurlRequest(BITPAY_VERIFY_URL, $data_transaction);

    if ($result['error']) {
        sm_channel('channel_errors', ['error_getway_get', $paymentEn, $result['error']]);
        redirect($base_url . '&msg=' . $media->text('error', $paymentEn));
    }

    $response = $result['response'];

    if (!isset($response->status) || $response->status != 1) {
        $db->update('transactions', ['status' => 0], ['id' => $code]);
        $bot->sm($fid, $media->text(['error_payment']));
        redirect($base_url);
    }

    $tracking_code = $_REQUEST['trans_id'];
    $card = $response->cardNum;

    if ($db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])) {
        redirect($base_url . '&msg=' . $media->text('duplicate_payment'));
    }

    // بررسی تطبیق کارت در صورت نیاز
    if ($user['payment_card']) {
        if (
            substr($card, 0, 6) !== substr($user['payment_card'], 0, 6) ||
            substr($card, -4) !== substr($user['payment_card'], -4)
        ) {
            $db->update('transactions', ['status' => 0], ['id' => $code]);
            $bot->sm($fid, $media->text(['not_pay_payment_card']));
            redirect($base_url);
        }
    }

    // افزایش موجودی
    $db->update('users_information', [
        'balance[+]' => $amount,
        'amount_paid[+]' => $amount
    ], ['id' => $fid]);

    // هدیه دعوت
    if ($user['referral_id'] && $section_status['commission']) {
        $gift = ($amount * $settings['commission']) / 100;
        $db->update('users_information', [
            'gift[+]' => $gift,
            'commission[+]' => $gift
        ], ['id' => $user['referral_id']]);
        $bot->sm($user['referral_id'], $media->text(['refral_gift_payment', $fid, $name, $amount, $gift]));
    }

    $db->update('transactions', [
        'status' => 1,
        'tracking_code' => $tracking_code,
        'getway' => $paymentEn,
        'type' => 'payment'
    ], ['id' => $code]);

    $bot->sm($fid, $media->text(['ok_payment', $tracking_code, $amount, $user['balance'], $paymentFa, $channels['show']]));
    $bot->sm($channels['payment'], "#تراکنش جدید بیت‌پی\n کاربر: <a href='tg://user?id=$fid'>$name</a>\nمبلغ: $amount\nکد پیگیری: $tracking_code\nکارت: $card\nIP: " . getip());

    header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $tracking_code . '&idbot=' . $idbot);
    exit;
}


/**
 * ارسال درخواست با cURL (مشترک بین درگاه‌ها)
 */
function sendCurlRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? http_build_query($data) : $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    return ['response' => json_decode($result), 'error' => $err];
}
