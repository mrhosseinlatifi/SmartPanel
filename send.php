<?php

require "config.php";

require_once ROOTPATH . "/include/db.php";
require ROOTPATH . "/bot_file/function/function.php";
require ROOTPATH . "/include/hkbot.php";
require ROOTPATH . "/include/jdf.php";
require_once ROOTPATH . "/media/index.php";

// Fetch job
$job = $db->get('jobs', '*', ['step[!]' => 'none']);
if (!$job) {
    update_option('last_cron_send', time());
    echo 'No Job';
    exit;
}
// Load settings
get_settings($settings);

if ($job['lock_job']) {
    handleLockedJob($db, $settings, $job);
    exit;
}

$jobId = $job['id'];
$db->update('jobs', ['lock_job' => 1], ['id' => $jobId]);
$bot = new hkbot(Token);

switch ($job['step']) {
    case 'sendall':
        processSendAll($db, $settings, $job);
        break;

    case 'fwd':
        processForward($db, $settings, $job);
        break;

    default:
        echo 'Unknown Step';
        $db->update('jobs', ['step'=>'none','lock_job' => 0,'last_job'=>time()], ['id' => $jobId]);
        break;
}

echo 'OK';

// Functions

function handleLockedJob($db, $settings, $job)
{
    echo 'Lock';
    $now = time();
    $last = $settings['last_cron_send'] + 90;

    if ($now >= $last) {
        $db->update('jobs', ['lock_job' => 0,'last_job'=>time()], ['id' => $job['id']]);
    }
}

function processSendAll($db, $settings, $job)
{
    global $bot;
    $media = new media();
    $sendpm_all = $media->atext('sended_pm');
    $usersCount = $db->count('users_information', ['block' => 0, 'block_bot' => 0]);
    $users = $db->select(
        "users_information",
        'user_id',
        ['block' => 0, 'block_bot' => 0, 'LIMIT' => [$job['user'], $settings['sall']]]
    );

    $info = json_decode($job['info'], true);
    foreach ($users as $user) {
        sendMessage($bot, $db, $user, $info);
    }

    finalizeJob($db, $settings, $job, $usersCount, $settings['sall'], $sendpm_all);
}

function processForward($db, $settings, $job)
{
    global $bot;
    $media = new media();
    $sendpm_all = $media->atext('forwarded_pm');
    $usersCount = $db->count('users_information', ['block' => 0, 'block_bot' => 0]);
    $users = $db->select(
        "users_information",
        'user_id',
        ['block' => 0, 'block_bot' => 0, 'LIMIT' => [$job['user'], $settings['fall']]]
    );

    $info = json_decode($job['info'], true);
    foreach ($users as $user) {
        $response = $bot->bot('forwardmessage', [
            'chat_id' => $user,
            'from_chat_id' => $info['from_chat'],
            'message_id' => $info['msgid']
        ]);
        handleBlockedUser($db, $response, $user);
    }

    finalizeJob($db, $settings, $job, $usersCount, $settings['fall'], $sendpm_all);
}

function sendMessage($bot, $db, $user, $info)
{
    $response = ($info['send'] === 'sm')
        ? $bot->sm($user, $info['text'])
        : $bot->bot($info['send'], [
            'chat_id' => $user,
            $info['type_file'] => $info['file_id'],
            'caption' => $info['caption']
        ]);

    handleBlockedUser($db, $response, $user);
}

function handleBlockedUser($db, $response, $user)
{
    if (isset($response['error']) && $response['error'] === 'Forbidden: bot was blocked by the user') {
        $db->update('users_information', ['block_bot' => 1], ['user_id' => $user]);
    }
}

function finalizeJob($db, $settings, $job, $usersCount, $increment, $successMessage)
{
    global $bot;
    $newUserCount = $job['user'] + $increment;

    if ($newUserCount >= $usersCount) {
        $db->update('jobs', ['step' => 'none', 'lock_job' => 0,'last_job'=>time()], ['id' => $job['id']]);
        
        $bot->sm($job["admin"], $successMessage);
    } else {
        $db->update('jobs', ['user' => $newUserCount, 'lock_job' => 0,'last_job'=>time()], ['id' => $job['id']]);
    }

    update_option('last_cron_send', time());
}
