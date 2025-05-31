<?php

define('ZIBAL_REQUEST_URL', 'https://gateway.zibal.ir/v1/request');     // Request payment token
define('ZIBAL_VERIFY_URL', 'https://gateway.zibal.ir/v1/verify');       // Verify payment
define('ZIBAL_PAYMENT_URL', 'https://gateway.zibal.ir/start/');         // Redirect to payment page

$paymentEn = 'zibal';
$paymentFa = 'زیبال';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

if ($type === 'get') {

    switch ($step) {
        case 2:
            $url = ZIBAL_REQUEST_URL;
            $data_transaction = [
                "merchant" => $result_payment['code'],
                "amount" => $amount * 10,
                "callbackUrl" => 'https://' . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                "description" => $media->text('desc_payment', [$fid, $name, $result_payment['name']]),
                "orderId" => $code
            ];

            if ($user['payment_card'] > 0) {
                $data_transaction['allowedCards'] = [$user['payment_card']];
            }

            if ($number) {
                $data_transaction['mobile'] = $number;
            }

            $result = sendCurlRequest($url, $data_transaction);
            if ($result['error']) {
                sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);
                redirect($base_url);
            } else {
                if ($result['response']['result'] == 100) {
                    $trackid = $result['response']['trackId'];

                    $decode_data['ip'] = $ip;;
                    $decode_data['payment'] = $paymentEn;

                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                    ], ['id' => $code]);
                    header('Location: ' . ZIBAL_PAYMENT_URL . $trackid);
                } else {
                    $msg = htmlentities($result['response']['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);
                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                header('Location: ' . ZIBAL_PAYMENT_URL . $trackid);
            } else {
                $base_url .= '&msg=' . $media->text('error_getway', $paymentEn);
                redirect($base_url);
            }
            break;

        default:
            $base_url .= '&msg=' . $media->text('error', $paymentEn);
            redirect($base_url);
            break;
    }
} elseif ($type === 'back') {

    $url = ZIBAL_VERIFY_URL;
    $data_transaction = [
        "merchant" => $result_payment['code'],
        "trackId" => $payment['tracking_code']
    ];

    $result = sendCurlRequest($url, $data_transaction);

    if ($result['error']) {
        sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
        $base_url .= '&msg=' . $media->text('error', $paymentEn);
        redirect($base_url);
    } else {
        $tracking_code = $result['response']['refNumber'];
        if (
            $result['response']['result'] == 100 &&
            !$db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])
        ) {
            $card = $result['response']['cardNumber'] ?? 0;
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
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "accept: application/json",
        "content-type: application/json",
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    return ['response' => json_decode($result, true), 'error' => $err];
}
