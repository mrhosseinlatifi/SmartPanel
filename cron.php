<?php
define('maindir', __DIR__);
require maindir . "/config.php";

//-----------------------------------//
require ROOTPATH . "/bot_file/function/function_tel.php";
require ROOTPATH . "/include/db.php";
require ROOTPATH . "/include/hkbot.php";
require ROOTPATH . "/include/jdf.php";
require ROOTPATH . "/include/usd_rate.php";
require ROOTPATH . "/media/index.php";
require ROOTPATH . "/media/admin.php";

check_cron_token();

$bot = new hkbot(Token);
$media_admin = new media_admin;

define('DIFF_TIME', get_option('DIFF_TIME', 0));

if (get_option('cron_auto_lock', 0)) {
    $last = (int) get_option('cron_auto_last', 0);
    if (time() - $last < 120) {
        exit('Lock');
    }
    update_option('cron_auto_lock', 0);
}

update_option('cron_auto_lock', 1);
update_option('cron_auto_last', time());

$usd_mode = get_option('usd_rate_mode', 'manual');

if ($usd_mode === 'auto') {
    $usd_interval = (int) get_option('usd_rate_interval', 60);
    $usd_last     = (int) get_option('usd_rate_last_update', 0);

    if (time() - $usd_last >= $usd_interval * 60) {
        $old_rate = (int) get_option('usd_rate', 0);
        $new_rate = fetch_usd_rate();
        if ($new_rate > 0) {
            apply_usd_rate_update($new_rate);
            notify_admins_usd_rate($bot, $db, $old_rate, $new_rate);
        } else {
            error_log('cron.php: usd_rate fetch failed');
        }
    }
}

function notify_admins_usd_rate($bot, $db, $old_rate, $new_rate)
{
    global $media_admin;

    $admins = $db->select('admins', ['user_id', 'access']);
    if (!$admins) {
        return;
    }

    $auto_starz = (int) get_option('auto_starz_rate', 0);
    $starz_rate = (float) get_option('starz_rate', 0);
    $msg = $media_admin->atext('usd_rate_auto_notify', [$new_rate, $old_rate, $auto_starz, $starz_rate]);

    foreach ($admins as $admin) {
        $access = json_decode($admin['access'], 1);
        if (!($access['main']['notification_price'] ?? 1)) {
            continue;
        }
        $bot->sm($admin['user_id'], $msg);
    }
}

update_option('cron_auto_lock', 0);
