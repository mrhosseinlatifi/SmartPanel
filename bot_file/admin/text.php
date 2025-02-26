<?php

function admin_text()
{
    extract($GLOBALS);
    switch ($text) {
        case $key['panel_admin']:
        case $key_admin['back_admin']:
        case '/panel':
        case '/start':
            admin_data(['step' => 'none', 'data' => 'none']);
            user_set_step('admin');
            sm_admin(['start_panel'], ['home', $access]);
            break;
        case $key_admin['stats']:
            $number_user = number_format($db->count('users_information')) ?: 0;
            $number_order = number_format($db->count('orders')) ?: 0;
            $ba = ($db->sum('users_information', 'balance') > 0) ? $db->sum('users_information', 'balance') : 0;
            $users_balance = number_format($ba) ?: 0;
            sm_admin(['stats', $number_user, $number_order, $users_balance], ['more_stats', 0]);
            break;
        case $key_admin['status']:
            check_allow('status');
            sm_admin(['status'], ['status', $section_status['main'], $section_status]);
            break;
        case $key_admin['sendall']:
            check_allow('sendall');
            admin_step('sendall');
            $sendstep = $db->has('jobs', ['step[!]' => "none"]);
            ($sendstep) ? sm_admin(['sendall_1', 1], ['send_panel']) : sm_admin(['sendall_1', 0], ['send_panel']);
            break;
        case $key_admin['userinfo']:
            check_allow('userinfo');
            admin_step('userinfo');
            sm_admin(['userinfo_1'], ['back_panel_all']);
            break;
        case $key_admin['settings']:
            check_allow('settings');
            admin_step('settings');
            sm_admin(['settings_1'], ['settings', in_array($fid, admins)]);
            break;
        case $key_admin['apis']:
            check_allow('apis');
            admin_step('apis');
            sm_admin(['apis_1'], ['api_panel']);
            break;
        case $key_admin['products']:
            check_allow('products');
            admin_step('products');
            sm_admin(['products_1'], ['products_panel']);
            break;
        case $key_admin['payments']:
            check_allow('payments');
            admin_step('payments');
            sm_admin(['payments_1'], ['payment_key']);
            break;
        case $key_admin['channels']:
            check_allow('channels');
            admin_step('channels');
            sm_admin(['channels_1'], ['channel_key']);
            break;
        case $key_admin['referral']:
            check_allow('referral');
            admin_step('referral');
            sm_admin(['referral_1'], ['referral_key']);
            break;
        case $key_admin['text']:
            check_allow('text');
            admin_step('text');
            sm_admin(['text_1'], ['text_key']);
            break;
        case '/last_cron':
        case '/cron':
            $cron = jdate('Y/m/d , H:i', $settings['last_cron_send']);
            $cron_done = jdate('Y/m/d , H:i', $settings['last_cron_orders']);
            sm_admin(['last_cron', $cron, $cron_done]);
            break;
        case '/cron_link':
            $se = 'https://' . $domin . '/send.php';
            $se2 = 'https://' . $domin . '/orders.php';
            sm_admin(['cron_link', $se, $se2]);
            break;
        case '/access':
            sm_admin(['access_tx'], ['show_access_admin', $access, $fid, true]);
            break;
        case $key_admin['back_user']:
            handleStart('start');
            break;
        default:
            sm_admin(['error_text_admin_home'], ['home', $access]);
            break;
    }
}
