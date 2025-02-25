<?php
require_once 'config.php';
require_once ROOTPATH . '/bot_file/function/function.php';
require_once ROOTPATH . '/include/hkbot.php';

function getDomain(): string {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $url = preg_replace('/^https?:\/\//', '', $url); // Remove protocol
    $url = preg_replace('/\?.*$/', '', $url); // Remove query parameters
    return dirname($url);
}

$ip = $_GET['ip'] ?? $_SERVER['SERVER_ADDR'];
$randomLength = rand(32, 64);
$randomCode = random_code($randomLength);
$hashedCode = md5($randomCode);
$webhookUrl = 'https://' . getDomain() . '/index.php';

$bot = new hkbot(Token);
$response = $bot->bot('setwebhook', [
    'url' => $webhookUrl,
    'drop_pending_updates' => true,
    'secret_token' => $randomCode,
    'ip_address' => $ip,
    'allowed_updates' => json_encode([
        "message", "edited_message", "channel_post", "edited_channel_post",
        "callback_query", "chat_member", "my_chat_member",
    ])
]);

if ($response['ok']) {
    $configContent = file_get_contents(ROOTPATH . '/config.php');
    $updatedConfig = str_replace(sec_code, $hashedCode, $configContent);
    file_put_contents(ROOTPATH . '/config.php', $updatedConfig);
    
    $bot->sm(admins[0], 'OK!');
    echo 'OK';
    // unlink('set.php'); // Uncomment if needed
} else {
    http_response_code(448);
    echo json_encode($response);
}
