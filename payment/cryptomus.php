<?php

define('CRYPTOMUS_PAYMENT_URL', 'https://api.cryptomus.com/v1/payment');     // Create payment
define('CRYPTOMUS_INFO_URL', 'https://api.cryptomus.com/v1/payment/info');   // Get payment info

$paymentEn = 'cryptomus';
$paymentFa = 'cryptomus';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

if ($type === 'get') {

    switch ($step) {
        case 2:
            $payment_key = $result_payment['code'];
            $ex = explode('|', $payment_key, 2);
            $cryptomus_key  = $ex[0] ?? '';
            $cryptomus_ipn  = $ex[1] ?? '';

            $dollar_price = get_option('usd_rate', 1);
            $dollar_price = (is_numeric($dollar_price) && $dollar_price > 0) ? $dollar_price : 1;
            $amount = $amount / $dollar_price;

            $amount = (string) round_up($amount, '-0.01');

            $life = 12 * 60 * 60;

            $data_transaction = [
                'amount' => $amount,
                'currency' => 'USD',
                'order_id' => $code,
                'url_return' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                'url_success' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=OK',
                'url_callback' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                'lifetime' => $life,
                'accuracy_payment_percent ' => 5,
                'success_invoice_url  ' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=OK',
                'fail_invoice_url  ' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=NOK',
                'redirect_to_invoice  ' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back',
                'is_payment_multiple' => false,
                'is_refresh' => true
            ];

            $jsonData = json_encode($data_transaction);
            $sign = md5(base64_encode($jsonData) . $cryptomus_ipn);


            $result = sendCurlRequest(CRYPTOMUS_PAYMENT_URL, $data_transaction,$cryptomus_key,$sign);

            if ($result['error']) {
                sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);

                redirect($base_url);
            } else {

                $response = is_array($result['response'] ?? null) ? $result['response'] : [];
                $responseResult = is_array($response['result'] ?? null) ? $response['result'] : [];
                if (($response['state'] ?? null) == 0 && !empty($responseResult['url']) && !empty($responseResult['uuid'])) {
                    $trackid = (string) $responseResult['uuid'];
                    $decode_data['ip'] = $ip;
                    $decode_data['payment'] = $paymentEn;

                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                        'type' => 'payment'
                    ], ['id' => $code]);

                    redirect_payment($responseResult['url']);
                } else {
                    $msg = $response['errors']['message'] ?? 'Invalid gateway response';
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);

                    redirect($base_url);
                }
            }
            break;

        case 3:
            $base_url .= '&msg=' . $media->text('error_getway', $paymentEn);
            redirect($base_url);
            break;

        default:
            redirect($base_url);
            break;
    }
} elseif ($type === 'back') {
    $payment_key = $result_payment['code'];
    $ex = explode('|', $payment_key, 2);
    $cryptomus_key  = $ex[0] ?? '';
    $cryptomus_ipn  = $ex[1] ?? '';

    if (!empty(file_get_contents('php://input'))) {
        $result_ipn = true;
        switch ($payment['status']) {
            case 1:
                break;
            case 3:
                $update_in = file_get_contents('php://input');
                $update_in_de = json_decode($update_in, true);
                $sign = is_array($update_in_de) && is_string($update_in_de['sign'] ?? null)
                    ? $update_in_de['sign']
                    : '';
                if (!is_array($update_in_de) || $sign === '') {
                    break;
                }
                unset($update_in_de['sign']);
                $hash = md5(base64_encode(json_encode($update_in_de, JSON_UNESCAPED_UNICODE)) . $cryptomus_ipn);

                if (hash_equals($hash, $sign)) {

                    $tracking_code = $update_in_de['uuid'];
                    $order_id = $update_in_de['order_id'];
                    $status_get = $update_in_de['status'];

                    if ((string) $order_id !== (string) $code) {
                        break;
                    }

                    switch ($status_get) {
                        case 'paid':
                        case 'paid_over':
                            $card = 0;
                            if ($tracking_code !== '' && markPaymentAsSuccessful($code, $tracking_code, $paymentEn)) {
                                $result_ok = true;
                            }
                            break;
                        case 'wrong_amount':
                            // کاربر مبلغ کمتر پرداخت کرده — رد می‌شود
                            break;
                        default:
                            $check_array = ['process', 'confirm_check', 'check', 'wrong_amount_waiting', 'locked'];
                            if (in_array($status_get, $check_array)) {
                                $result_ipn = true;
                            }
                            break;
                    }
                }
                break;
        }
    } else {
        switch ($payment['status']) {
            case 1:
                break;
            case 3:
                $params = array(
                    'uuid' => $payment['tracking_code'],
                    'order_id' => (string) $payment['id'],
                );
                
                $jsonData = json_encode($params);
                $sign = md5(base64_encode($jsonData) . $cryptomus_ipn);
                
                $result = sendCurlRequest(CRYPTOMUS_INFO_URL, $params,$cryptomus_key,$sign);
                
                $response = is_array($result['response'] ?? null) ? $result['response'] : [];
                $responseResult = is_array($response['result'] ?? null) ? $response['result'] : [];
                if (($response['state'] ?? null) == 0) {
                    if (($responseResult['uuid'] ?? null) == $payment['tracking_code']) {
                        
                        $status_get = $responseResult['status'] ?? '';
                        $tracking_code = (string) ($responseResult['uuid'] ?? '');

                        switch ($status_get) {
                            case 'paid':
                            case 'paid_over':
                                $card = 0;
                                if (markPaymentAsSuccessful($code, $tracking_code, $paymentEn)) {
                                    $result_ok = true;
                                }
                                break;
                            case 'wrong_amount':
                                // کاربر مبلغ کمتر پرداخت کرده — رد می‌شود
                                break;
                            default:
                                $check_array = ['process', 'confirm_check', 'check', 'wrong_amount_waiting', 'locked'];
                                if (in_array($status_get, $check_array)) {
                                    $result_ipn = true;
                                }
                                break;
                        }
                    }
                }
                break;
        }
    }
}

/**
 * Function to handle cURL requests
 */
function sendCurlRequest($url, $data, $key, $sign)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "merchant: " . $key,
        "sign: " . $sign,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    return ['response' => json_decode($result, true), 'error' => $err];
}
