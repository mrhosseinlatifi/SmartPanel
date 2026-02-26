<?php
define('HELEKET_CREATE_URL', 'https://api.heleket.com/v1/payment');
define('HELEKET_INFO_URL', 'https://api.heleket.com/v1/payment/info');
define('HELEKET_PAYMENT_URL', 'https://pay.heleket.com/pay/');


$paymentEn = 'heleket';
$paymentFa = 'هلکت';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

if ($type === 'get') {
    switch ($step) {
        case 2:
            $payment_key = $result_payment['code'];
            $ex = explode('|', $payment_key);
            $key = $ex[0];
            $ipn = $ex[1];

            define('HELEKET_IPN', $ipn);
            define('HELEKET_API_KEY', $key);

            $dollar_price = get_option('usd_rate', 1);
            $dollar_price = (is_numeric($dollar_price) && $dollar_price > 0) ? $dollar_price : 1;
            $amount_usd = $amount / $dollar_price;
            $amount_usd = (string) round_up($amount_usd, '-0.01');


            $data_transaction = [
                'amount' => $amount_usd,
                'currency' => 'USD',
                'order_id' => $code,
                'url_callback' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=ipn',
                'url_return' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=check',
            ];

            $result = sendCurlRequest(HELEKET_CREATE_URL, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);
                redirect($base_url);
            } else {
                if (!empty($result['response']) && isset($result['response']['state']) && $result['response']['state'] == '0') {
                    $trackid = $result['response']['result']['uuid'];
                    $payment_url = HELEKET_PAYMENT_URL . $trackid;

                    $decode_data['ip'] = $ip;
                    $decode_data['payment'] = $paymentEn;
                    $decode_data['price_amount'] = $amount_usd;

                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                        'type' => 'payment'
                    ], ['id' => $code]);

                    redirect_payment($payment_url);
                } else {
                    $msg = $result['response']['message'] ?? $result['response']['error'] ?? 'Unknown error';
                    sm_channel('channel_errors', ['error_getway_get', $paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);
                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                $payment_url = HELEKET_PAYMENT_URL . $trackid;
                redirect_payment($payment_url);
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
    $payment_key = $result_payment['code'];
    $ex = explode('|', $payment_key);
    $key = $ex[0];
    $ipn = $ex[1];

    define('HELEKET_IPN', $ipn);
    define('HELEKET_API_KEY', $key);

    if (isset($_GET['status'])) {
        $status = $_GET['status'];

        switch ($status) {
            case 'ipn':
                $result_ipn = true;

                $request_json = file_get_contents('php://input');
                $request_data = json_decode($request_json, true);

                if (!empty($request_json) && !empty($request_data)) {
                    $received_signature = $request_data['sign'] ?? '';

                    $data_for_hash = $request_data;
                    unset($data_for_hash['sign']);

                    $sorted_json = json_encode($data_for_hash, JSON_UNESCAPED_UNICODE);
                    $calculated_signature = md5(base64_encode($sorted_json) . HELEKET_IPN);

                    if (hash_equals($calculated_signature, $received_signature) && $step == '3') {
                        $payment_status = $request_data['status'] ?? '';
                        $tracking_code = $request_data['uuid'];

                        if ($payment_status === 'paid') {
                            $is_duplicate = $db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn, 'status' => 1]);

                            if (!$is_duplicate) {
                                $result_ok = true;
                                $db->update('transactions', [
                                    'status' => 1,
                                    'tracking_code' => $tracking_code,
                                    'getway' => $paymentEn,
                                    'type' => 'payment'
                                ], ['id' => $code]);
                            }
                        } else {
                            $result_ipn = true;
                        }
                    } else {
                        $result_ipn = true;
                    }
                }
                break;

            case 'check':
                if ($step == '3') {
                    $payment_id = $payment['tracking_code'];

                    if (!empty($payment_id)) {
                        $data_transaction = [
                            'uuid' => $payment_id
                        ];
                        $result = sendCurlRequest(HELEKET_INFO_URL, $data_transaction);
                        if (!empty($result['response']) && isset($result['response']['state'])) {
                            $payment_status = $result['response']['result']['status'];
                            $tracking_code = $result['response']['result']['uuid'];

                            $is_duplicate = $db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn, 'status' => 1]);

                            if (
                                $payment_status === 'paid' && !$is_duplicate
                            ) {

                                $result_ok = true;
                                $db->update('transactions', [
                                    'status' => 1,
                                    'tracking_code' => $tracking_code,
                                    'getway' => $paymentEn,
                                    'type' => 'payment'
                                ], ['id' => $code]);
                            } else {
                                $check_array = ['confirm_check'];
                                if (in_array($payment_status, $check_array)) {
                                    $result_ipn = true;
                                }
                            }
                        } else {
                        }
                    }
                } else {
                    if ($step == '1') {
                        header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $payment['tracking_code'] . '&idbot=' . $idbot);
                        exit;
                    }
                }
                break;
        }
    }
}

function sendCurlRequest($url, $data = [], $method = 'POST')
{
    $body = json_encode($data, JSON_UNESCAPED_UNICODE);

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json;charset=UTF-8',
        'Content-Length: ' . strlen($body),
        'merchant: ' . HELEKET_API_KEY,
        'sign: ' . md5(base64_encode($body) . HELEKET_IPN)
    ];

    $ch = curl_init();

    if ($method === 'GET') {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        if (!empty($data) && is_array($data)) {
            $url .= '?' . http_build_query($data);
        }
    } else {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $result = curl_exec($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response = json_decode($result, true);

    if ($http_code >= 400) {
        $err = $err ?: "HTTP Error: $http_code";
    }

    return ['response' => $response, 'error' => $err, 'http_code' => $http_code];
}
