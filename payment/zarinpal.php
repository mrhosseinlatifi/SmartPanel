<?php

define('ZARINPAL_REQUEST_URL', 'https://api.zarinpal.com/pg/v4/payment/request.json');  // Request payment token
define('ZARINPAL_VERIFY_URL', 'https://api.zarinpal.com/pg/v4/payment/verify.json');    // Verify payment
define('ZARINPAL_PAYMENT_URL', 'https://www.zarinpal.com/pg/StartPay/');                // Redirect to payment page

$paymentEn = 'zarinpal';
$paymentFa = 'زرین پال';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

if ($type === 'get') {

    switch ($step) {
        case 2:
            $url = ZARINPAL_REQUEST_URL;

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
                
                $response = is_array($result['response'] ?? null) ? $result['response'] : [];
                $responseData = is_array($response['data'] ?? null) ? $response['data'] : [];
                if (($responseData['code'] ?? null) == 100 && !empty($responseData['authority'])) {
                    $trackid = (string) $responseData['authority'];
                    $decode_data['ip'] = $ip; ; 
                    $decode_data['payment'] = $paymentEn; 
                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                        'type' => 'payment'
                    ], ['id' => $code]);

                    redirect_payment(ZARINPAL_PAYMENT_URL . $trackid);
                } else {
                    $msg = $response['errors']['message'] ?? 'Invalid gateway response';
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);

                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                redirect_payment(ZARINPAL_PAYMENT_URL . $trackid);
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
    $url = ZARINPAL_VERIFY_URL;
    $authority = $_GET['Authority'] ?? '';
    $status = $_GET['Status'] ?? '';
    if (is_string($authority) && $payment['tracking_code'] === $authority) {
        if ($status === 'OK') {

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
                $response = is_array($result['response'] ?? null) ? $result['response'] : [];
                $responseData = is_array($response['data'] ?? null) ? $response['data'] : [];
                if (($responseData['code'] ?? null) == 100 && !empty($responseData['ref_id'])) {
                    $tracking_code = (string) $responseData['ref_id'];
                    $card = $responseData['card_pan'] ?? 0;
                    if (markPaymentAsSuccessful($code, $tracking_code, $paymentEn)) {
                        $result_ok = true;
                    }
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

    return [
        'response' => is_string($result) ? json_decode($result, true) : null,
        'error' => $err,
    ];
}
