<?php
http_response_code(200);

$url = $_SERVER['SCRIPT_URI'] ?? ('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

$url = preg_replace('/index\.php$/', '', $url);

if ($_SERVER['HTTPS'] != "on" && $_SERVER['SERVER_PORT'] != 443) {
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
        CURLOPT_SSL_VERIFYPEER      => true,
        CURLOPT_SSL_VERIFYHOST      => 2,
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
