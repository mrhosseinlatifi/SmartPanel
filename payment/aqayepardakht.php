<?php

define('AQAYEPARDAKHT_CREATE_URL', 'https://panel.aqayepardakht.ir/api/v2/create');    // Create payment token
define('AQAYEPARDAKHT_VERIFY_URL', 'https://panel.aqayepardakht.ir/api/v2/verify');    // Verify payment
define('AQAYEPARDAKHT_PAYMENT_URL', 'https://panel.aqayepardakht.ir/startpay/');       // Redirect to payment page

$paymentEn = 'aqayepardakht';
$paymentFa = 'آقای پرداخت';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot . '&msg=' . $media->text('error', $paymentEn);

if ($type === 'get') {

    switch ($step) {
        case 2:
            $url = AQAYEPARDAKHT_CREATE_URL;

            $data_transaction = [
                "pin" => $result_payment['code'],
                "invoice_id" => $code,
                "amount" => $amount,
                "callback" => 'https://' . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                "description" => $media->text('desc_payment', [$fid, $name, $result_payment['name']]),
            ];

            if ($user['payment_card'] > 0) {
                $data_transaction['card_number'] = $user['payment_card'];
            }

            if ($number) {
                $data_transaction['mobile'] = $number;
            }

            $result = sendCurlRequest($url, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
                redirect($base_url);
            } else {
                if ($result['response']['status'] == 'success') {
                    $trackid = $result['response']['transid'];
                    $decode_data['ip'] = $ip;;
                    $decode_data['payment'] = $paymentEn;
                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                    ], ['id' => $code]);
                    redirect_payment(AQAYEPARDAKHT_PAYMENT_URL . $trackid);
                } else {
                    $msg = $result['response']['code'];
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, $msg]);
                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                redirect_payment(AQAYEPARDAKHT_PAYMENT_URL . $trackid);
            }
            break;

        default:
            redirect($base_url);
            break;
    }
} elseif ($type === 'back') {

    $url = AQAYEPARDAKHT_VERIFY_URL;

    $data_transaction = [
        "pin" => $result_payment['code'],
        "transid" => $payment['tracking_code'],
        "amount" => $amount,
    ];

    $result = sendCurlRequest($url, $data_transaction);

    if ($result['error']) {
        sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
        redirect($base_url);
    } else {
        $tracking_code = $_POST['tracking_number'];
        if (
            $result['response']['status'] == 'success' &&
            !$db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])
        ) {

            $card = $_POST['cardnumber'];

            $db->update('transactions', [
                'status' => 1,
                'tracking_code' => $tracking_code,
                'getway' => $paymentEn
            ], ['id' => $code]);

            $result_ok = true;
        }
    }
}

/**
 * Function to handle cURL requests
 */
function sendCurlRequest($url, $data)
{
    $data = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "accept: application/json",
        "content-type: application/json",
        'Content-Length: ' . strlen($data),
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    return ['response' => json_decode($result, true), 'error' => $err];
}
