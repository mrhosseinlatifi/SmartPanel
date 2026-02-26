<?php
$paymentEn = 'nowpayments';
$paymentFa = 'nowpayments';
$base_url = 'https://' . $domin . '/payment/show.php?NOK&idbot=' . $idbot;

$url_1 = "https://api.nowpayments.io/v1/invoice";
$url_2 = "https://api.nowpayments.io/v1/payment/";
$url_3 = "https://nowpayments.io/payment/?iid=";

if ($type === 'get') {
    switch ($step) {
        case 2:
            $url = $url_1;
            $payment_key = $result_payment['code'];
            $ex = explode('|', $payment_key);
            $key = $ex[0];
            $ipn = $ex[1];

            define('now_key', $key);
            define('now_ipn', $ipn);

            $dollar_price = get_option('usd_rate', 1);
            $dollar_price = (is_numeric($dollar_price) && $dollar_price > 0) ? $dollar_price : 1;
            $amount = $amount / $dollar_price;
            $amount = (string) round_up($amount, '-0.01');

            $data_transaction = [
                'order_id' => $code,
                'price_amount' => $amount,
                'price_currency' => 'usd',
                'order_description' => "User : " . $fid,
                'ipn_callback_url' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=ipn',
                'success_url' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=OK',
                'cancel_url' => "https://" . $domin . '/payment/index.php?file=' . $paymentEn . '&code=' . $code . '&action=back&status=NOK',
                'is_fee_paid_by_user' => false
            ];

            $result = sendCurlRequest($url, $data_transaction);

            if ($result['error']) {
                sm_channel('channel_errors', ['curl_payment_error', $paymentEn, $result['error']]);
                $base_url .= '&msg=' . $media->text('error', $paymentEn);
                redirect($base_url);
            } else {
                if (!empty($result['response']) && !empty($result['response']['invoice_url'])) {
                    $trackid = $result['response']['id'];
                    $decode_data['ip'] = $ip;
                    $decode_data['payment'] = $paymentEn;
                    $decode_data['price_amount'] = $result['response']['price_amount'];

                    $db->update('transactions', [
                        'status' => 3,
                        'data[JSON]' => $decode_data,
                        'tracking_code' => $trackid,
                        'getway' => $paymentEn,
                        'type' => 'payment'
                    ], ['id' => $code]);


                    redirect_payment($url_3 . $trackid);
                } else {
                    $msg = $result['response']['errors']['message'] ?? 'Unknown error';
                    sm_channel('error_getway_get', [$paymentEn, $msg]);
                    $base_url .= '&msg=' . $media->text('error', $paymentEn);
                    redirect($base_url);
                }
            }
            break;

        case 3:
            if ($payment['getway'] == $paymentEn) {
                $trackid = $payment['tracking_code'];
                redirect_payment($url_3 . $trackid);
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

    define('now_key', $key);
    define('now_ipn', $ipn);

    if (isset($_GET['status'])) {
        $s = $_GET['status'];

        switch ($s) {
            case 'ipn':
                $result_ipn = true;

                $request_json = file_get_contents('php://input');
                $request_data = json_decode($request_json, true);
                $recived_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'] ?? '';

                if (!empty($request_json)) {
                    ksort($request_data, SORT_STRING);
                    $sorted_request_json = json_encode($request_data, JSON_UNESCAPED_SLASHES);
                    $hmac = hash_hmac("sha512", $sorted_request_json, now_ipn);

                    if ($hmac === $recived_hmac && $step == '3') {
                        $url = $url_2 . $request_data['payment_id'];
                        $result = sendCurlRequest($url);

                        $status = $result['response']['payment_status'];
                        $tracking_code = $result['response']['payment_id'];

                        if ($status === 'finished' && !$db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])) {
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
                }
                break;

            case 'OK':
                if ($step == '3') {
                    $np_id = $_GET['NP_id'] ?? 0;
                    $url = $url_2 . $np_id;
                    $result = sendCurlRequest($url);

                    $status = $result['response']['payment_status'];
                    $tracking_code = $result['response']['payment_id'];



                    if (
                        $status === 'finished' &&
                        !$db->has('transactions', ['tracking_code' => $tracking_code, 'type' => 'payment', 'getway' => $paymentEn])
                    ) {
                        $result_ok = true;
                        $db->update('transactions', [
                            'status' => 1,
                            'tracking_code' => $tracking_code,
                            'getway' => $paymentEn,
                            'type' => 'payment'
                        ], ['id' => $code]);
                    } else {
                        $check_array = ['sending', 'processing', 'waiting', 'creating', 'confirmed'];
                        if (in_array($status, $check_array)) {
                            $result_ipn = true;
                        }
                    }
                } else {
                    // If payment is finished but already recorded as confirmed in DB,
                    // avoid re-sending bot messages: redirect user to success page.
                    if ($step == '1') {
                        header('Location: https://' . $domin . '/payment/show.php?OK&code=' . $payment['tracking_code'] . '&idbot=' . $idbot);
                        exit;
                    }
                }
                break;

            case 'NOK':
                break;
        }
    }
}

/**
 * Function to handle cURL requests
 */

function sendCurlRequest($url, $data = [])
{
    $headers = [
        "accept: application/json",
        "content-type: application/json",
        'X-API-KEY: ' . now_key,
    ];

    $ch = curl_init();

    if (empty($data)) {
        curl_setopt($ch, CURLOPT_HTTPGET, true);
    } else {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    $response = json_decode($result, true);

    return ['response' => $response, 'error' => $err];
}
