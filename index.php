<?php
http_response_code(200);

$ip = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['HTTP_X_REAL_IP'] ?? ($_SERVER['HTTP_AR_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'])));

$url = $_SERVER['SCRIPT_URI'] ?? ('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

$url = rtrim($url, 'index.php');

$ipRange1Start = "149.154.160.0";
$ipRange1End = "149.154.176.0";
$ipRange2Start = "91.108.4.0";
$ipRange2End = "91.108.8.0";

if (($ip < $ipRange1Start || $ip > $ipRange1End) && ($ip < $ipRange2Start || $ip > $ipRange2End)) {
    exit(include "page.php");
} elseif ($_SERVER['HTTPS'] != "on" && $_SERVER['SERVER_PORT'] != 443) {
    exit(include "page.php");
}
$data = file_get_contents('php://input');

$secret_token = isset($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN']) ? $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] : null;
if ($secret_token) {
    $url = $url . '/bot_file/index.php?hash=' . $secret_token;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POSTFIELDS          => $data,
        CURLOPT_TIMEOUT             => 10,
        CURLOPT_RETURNTRANSFER      => true,
        CURLOPT_SSL_VERIFYPEER      => false,
        CURLOPT_SSL_VERIFYHOST      => false,
        CURLOPT_CONNECTTIMEOUT      => 10,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'content-type: application/json'
        ]
    ]);
    curl_exec($ch);
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        $log = date('Y-m-d H:i:s') . " CURL ERROR: $error";
        error_log($log);
    }
    curl_close($ch);
} else {
    exit(include "page.php");
}
