<?php

$paymentEn = 'zarinpal';
$paymentFa = 'زرین پال';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

$url_1 = "https://api.zarinpal.com/pg/v4/payment/request.json";
$url_2 = "https://api.zarinpal.com/pg/v4/payment/verify.json";
$url_3 = "https://www.zarinpal.com/pg/StartPay/";

if ($type === 'get') {

    switch ($step) {
        case 2:
            $url = $url_1;

            $data_transaction = [
                "merchant_id" => $result_payment['code'],
                "currency" => "IRT",
                "amount" => $amount,
                "callback_url" => 'https://' . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                "description" => $media->text('desc_payment', [$fid, $name, $result_payment['name']]),
            ];

            if ($user['payment_card'] > 0) {
                $data_transaction['metadata']['card_pan'] = [$user['payment_card']];
            }

            if ($number) {
                $data_transaction['metadata']['mobile'] = $number;
            }

            $result = sendCurlRequest($url, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors',['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);

                redirect($base_url);
            } else {
                
                if ($result['response']['data']['code'] == 100) {
                    $trackid = $result['response']['data']['authority'];
                    $decode_data['ip'] = $ip; ; 
                    $decode_data['payment'] = $paymentEn; 
                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                    ], ['id' => $code]);

                    header('Location: ' . $url_3 . $trackid);
                } else {
                    $msg = $result['response']['errors']['message'];
                    sm_channel('error_getway_get',[$paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);

                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                header('Location: ' . $url_3 . $trackid);
            } else {
                $base_url .= '&msg=' . $media->text('error_getway', $paymentEn);
                redirect($base_url);
            }
            break;

        default:
            redirect($base_url);
            break;
    }
} elseif ($type === 'back') {
    $url = "https://payment.zarinpal.com/pg/v4/payment/verify.json";
    if ($payment['tracking_code'] == $_REQUEST['Authority']) {
        if ($_REQUEST['Status'] == 'OK') {

            $data_transaction = [
                "merchant_id" => $result_payment['code'],
                "amount" => $amount,
                "authority" => $payment['tracking_code']
            ];

            $result = sendCurlRequest($url, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors',['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);

                redirect($base_url);
            } else {
                $tracking_code = $result['response']['data']['ref_id'];
                if (
                    $result['response']['data']['code'] == 100 &&
                    !$db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])
                ) {

                    $card = $result['response']['data']['card_pan'];

                    $db->update('transactions', [
                        'status' => 1,
                        'tracking_code' => $tracking_code,
                        'getway' => $paymentEn
                    ], ['id' => $code]);

                    $result_ok = true;
                }
            }
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
