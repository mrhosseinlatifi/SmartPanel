<?php
function admin_steps()
{
    extract($GLOBALS);
    switch ($text) {
        case $key['panel_admin']:
        case $key_admin['back_admin']:
        case '/panel':
        case '/start':
        case $key['back']:
            admin_data(['step' => 'none', 'data' => 'none']);
            user_set_step('admin');
            sm_admin(['start_panel'], ['home', $access]);
            exit;
            break;
    }
    switch ($astep) {
        case 'sendall':
            switch ($text) {
                case '/cancelall':
                    $db->update('jobs', ['step' => "none"], ['step[!]' => "none"]);
                    sm_admin(['sendall_1']);
                    break;
                case '/sendmsg':
                    $send = $db->get('jobs', '*', ['step[!]' => "none"]);
                    sendallmsg($fid, $send);
                    break;
                case $key_admin['sendall_sendall']:
                    admin_step('sendall_2');
                    sm_admin(['sendall_2'], ['back_panel']);
                    break;
                case $key_admin['sendall_forall']:
                    admin_step('sendall_3');
                    sm_admin(['sendall_3'], ['back_panel']);
                    break;
                case $key_admin['sendall_edit']:
                    admin_step('sendall_4');
                    sm_admin(['sendall_4', $settings['sall'], $settings['fall']], ['sendall_edit']);
                    break;
                default:
                    $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                    ($sendstep) ? sm_admin(['sendall_1', 1], ['send_panel']) : sm_admin(['sendall_1', 0], ['send_panel']);
                    break;
            }
            break;
        case 'sendall_2':
            if ($text == $key_admin['back_admin_before']) {
                check_allow('sendall');
                admin_step('sendall');
                $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                ($sendstep) ? sm_admin(['sendall_1', 1], ['send_panel']) : sm_admin(['sendall_1', 0], ['send_panel']);
            } else {
                $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                if (!$sendstep) {
                    if (isset($text)) {
                        $data_send = ['step' => 'sendall', 'info[JSON]' => ['send' => 'sm', 'text' => $text], 'user' => '0', 'admin' => $fid];
                        $db->insert('jobs', $data_send);
                        admin_step('sendall');
                        sm_admin(['sendall_5'], ['send_panel']);
                    } else {
                        $types = ['video', 'photo', 'audio', 'voice', 'document'];
                        $file_id = null;
                        $caption = '';

                        $true = true;

                        foreach ($types as $i) {
                            if (isset($update['message'][$i])) {
                                $type = $update['message'][$i];
                                $caption = $update['message']['caption'] ?? '';
                                $file_id = ($i == 'photo') ? end($type)['file_id'] : $type['file_id'];
                                break;
                            } else {
                                $true = false;
                            }
                        }

                        if ($true) {
                            $send = str_replace($types, ['sendvideo', 'sendphoto', 'sendaudio', 'sendvoice', 'senddocument'], $i);
                            $data_send = ['step' => 'sendall', 'info[JSON]' => ['send' => $send, 'file_id' => $file_id, 'type_file' => $i, 'caption' => $caption], 'user' => '0', 'admin' => $fid];
                            $db->insert('jobs', $data_send);
                            admin_step('sendall');
                            sm_admin(['sendall_5'], ['send_panel']);
                        } else {
                            sm_admin(['error_sendall']);
                        }
                    }
                } else {
                    admin_step('sendall');
                    sm_admin(['sendall_6'], ['send_panel']);
                }
            }
            break;
        case 'sendall_3':
            if ($text == $key_admin['back_admin_before']) {
                check_allow('sendall');
                admin_step('sendall');
                $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                ($sendstep) ? sm_admin(['sendall_1', 1], ['send_panel']) : sm_admin(['sendall_1', 0], ['send_panel']);
            } else {
                $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                if (!$sendstep) {
                    $data_send =  ['step' => 'fwd', 'info[JSON]' => ['msgid' => $message_id, 'from_chat' => $fid], 'user' => '0', 'admin' => $fid];
                    $db->insert('jobs', $data_send);
                    admin_step('sendall');
                    sm_admin(['sendall_5'], ['send_panel']);
                } else {
                    admin_step('sendall');
                    sm_admin(['sendall_6'], ['send_panel']);
                }
            }
            break;
        case 'sendall_4':
            if ($text == $key_admin['back_admin_before']) {
                check_allow('sendall');
                admin_step('sendall');
                $sendstep = $db->has('jobs', ['step[!]' => "none"]);
                ($sendstep) ? sm_admin(['sendall_1', 1], ['send_panel']) : sm_admin(['sendall_1', 0], ['send_panel']);
            } else {
                if ($text == $key_admin['sendall_edit_sendall']) {
                    admin_data(['step' => 'sendall_5', 'data' => 'sall']);
                    sm_admin(['sendall_7', $settings['sall']], ['back_panel']);
                } elseif ($text == $key_admin['sendall_edit_forall']) {
                    admin_data(['step' => 'sendall_5', 'data' => 'fall']);
                    sm_admin(['sendall_7', $settings['fall']], ['back_panel']);
                }
            }
            break;
        case 'sendall_5':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('sendall_4');
                sm_admin(['sendall_4', $settings['sall'], $settings['fall']], ['sendall_edit']);
            } else {
                $text = convertnumber($text);
                if (is_numeric($text)) {
                    update_option($admin['data'], $text);
                    admin_step('sendall_4');
                    sm_admin(['sendall_9', $text], ['sendall_edit']);
                }
            }
            break;
        case 'userinfo':
            $text = convertnumber($text);
            if (is_numeric($text)) {
                $result = $db->get('users_information', '*', ['OR' => ['user_id' => $text, 'number' => $text]]);
                if ($result) {
                    $id = $result['user_id'];
                    $name = $bot->getChatMember($id);
                    admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                    sm_admin(['userinfo_2', $result, $name], ['userinfo_data', $id]);
                    sm_admin(['userinfo_3'], ['userinfo_panel']);
                } else {
                    sm_admin(['userinfo_4']);
                }
            } else {
                sm_admin(['userinfo_4']);
            }
            break;
        case 'userinfo_2':
            $admin_data = json_decode($admin['data'], 1);
            $id = $admin_data['user_id'];
            switch ($text) {
                case $key_admin['userinfo_pm']:
                    $admin_data['type'] = 'pm';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_5'], ['back_panel']);
                    break;
                case $key_admin['userinfo_dis']:
                    $admin_data['type'] = 'discount';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_6'], ['back_panel']);
                    break;
                case $key_admin['userinfo_up']:
                    $admin_data['type'] = 'up_coin';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_7'], ['back_panel']);
                    break;
                case $key_admin['userinfo_down']:
                    $admin_data['type'] = 'down_coin';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_8'], ['back_panel']);
                    break;
                case $key_admin['userinfo_card']:
                    $admin_data['type'] = 'set_card';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_9'], ['back_panel']);
                    break;
                case $key_admin['userinfo_phone']:
                    $admin_data['type'] = 'set_number';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_10'], ['back_panel']);
                    break;
                case $key_admin['userinfo_ref']:
                    $admin_data['type'] = 'set_ref';
                    admin_data(['step' => 'userinfo_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_11'], ['back_panel']);
                    break;
                case $key_admin['userinfo_ban']:
                    admin_step('userinfo_2');
                    user_set_data(['block' => 1], $id);
                    sm_admin(['userinfo_12']);
                    sm_to_user(['block'], null, $id);
                    break;
                case $key_admin['userinfo_unban']:
                    admin_step('userinfo_2');
                    user_set_data(['block' => 0], $id);
                    sm_admin(['userinfo_13']);
                    sm_to_user(['unblock'], null, $id);
                    break;
                case $key_admin['back_admin_before']:
                    admin_step('userinfo');
                    sm_admin(['userinfo_1'], ['back_panel_all']);
                    break;
                case $key_admin['userinfo']:
                    $result = $db->get('users_information', '*', ['user_id' => $id]);
                    $id = $result['user_id'];
                    $name = $bot->getChatMember($id);
                    sm_admin(['userinfo_2', $result, $name], ['userinfo_data', $id]);
                    break;
            }
            break;
        case 'userinfo_3':
            $admin_data = json_decode($admin['data'], 1);
            $id = $admin_data['user_id'];
            $type = $admin_data['type'];
            if ($text == $key_admin['back_admin_before']) {
                admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                sm_admin(['userinfo_3'], ['userinfo_panel']);
                return;
            }
            switch ($type) {
                case 'pm':
                    if (isset($text)) {
                        sm_to_user(['support_pm2', $text], ['fast_support'], $id);
                    } else {
                        $types = ['video', 'photo', 'audio', 'voice', 'document'];
                        foreach ($types as $i) {
                            if (isset($update['message'][$i])) {
                                $type = $update['message'][$i];
                                @$caption = $update['message']['caption'];
                                if ($i == 'photo') {
                                    $file_id = $type[count($type) - 1]['file_id'];
                                } else {
                                    $file_id = $type['file_id'];
                                }
                                break;
                            }
                        }
                        $send = str_replace($types, ['sendvideo', 'sendphoto', 'sendaudio', 'sendvoice', 'senddocument'], $i);
                        $r = $bot->bot($send, [
                            'chat_id' => $id,
                            $i => $file_id,
                            'caption' => $media->atext('support_pm2', $caption),
                            'parse_mode' => 'Html',
                            'reply_markup' => json_encode($media->akeys('fast_support')),
                        ]);
                    }
                    admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                    sm_admin(['userinfo_14'], ['userinfo_panel']);
                    break;
                case 'discount':
                    $text = convertnumber($text);
                    $text = str_replace('-', '', $text);
                    if ($text >= 0) {
                        user_set_data(['discount' => $text], $id);
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        sm_admin(['userinfo_15', $text], ['userinfo_panel']);
                    } else {
                        sm_admin(['userinfo_16']);
                    }
                    break;
                case 'up_coin':
                    $text = convertnumber($text);
                    if ($text > 0) {
                        $admin_data['q'] = $text;
                        admin_data(['step' => 'userinfo_4', 'data[JSON]' => $admin_data]);
                        sm_admin(['userinfo_21', $text], ['ok_cancel_admin_panel']);
                    } else {
                        sm_admin(['userinfo_16']);
                    }
                    break;
                case 'down_coin':
                    $text = convertnumber($text);
                    $text = str_replace('-', '', $text);
                    if ($text > 0) {
                        $text = str_replace('-', '', $text);
                        $admin_data['q'] = $text;
                        admin_data(['step' => 'userinfo_4', 'data[JSON]' => $admin_data]);
                        sm_admin(['userinfo_21', $text], ['ok_cancel_admin_panel']);
                    } else {
                        sm_admin(['userinfo_16']);
                    }
                    break;
                case 'set_card':
                    $admin_data['q'] = $text;
                    admin_data(['step' => 'userinfo_4', 'data[JSON]' => $admin_data]);
                    sm_admin(['userinfo_22', $text], ['ok_cancel_admin_panel']);
                    break;
                case 'set_number':
                    if ($db->has('users_information', ['number' => $text]) || $text == '0') {
                        user_set_data(['number' => $text], $id);
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        sm_admin(['userinfo_20'], ['userinfo_panel']);
                    } else {
                        sm_admin(['not_user']);
                    }
                    break;
                case 'set_ref':
                    if ($db->has('users_information', ['user_id' => $text]) && $text != $id || $text == '0') {
                        user_set_data(['referral_id' => $text], $id);
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        sm_admin(['userinfo_20'], ['userinfo_panel']);
                    } else {
                        sm_admin(['not_user']);
                    }
                    break;
            }
            break;
        case 'userinfo_4':
            $admin_data = json_decode($admin['data'], 1);
            $id = $admin_data['user_id'];
            $type = $admin_data['type'];
            $q = $admin_data['q'];
            if ($text == $key_admin['back_admin_before']) {

                admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                sm_admin(['userinfo_3'], ['userinfo_panel']);

                return;
            }

            if ($text == $key_admin['ok_admin']) {
                switch ($type) {
                    case 'set_card':
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        sm_to_user(['ok_verify', $q], null, $id);
                        user_set_data(['payment_card' => $q], $id);
                        sm_admin(['userinfo_20'], ['userinfo_panel']);
                        break;
                    case 'down_coin':
                        user_set_data(['balance[-]' => $q], $id);
                        $us = $db->get('users_information', 'balance', ['user_id' => $id]);
                        $old = $us + $q;
                        $new = $us;
                        insertTransaction('managment', $id, $old, $new, $q, 'decrease', $fid);
                        sm_to_user(['down_coin', $q, $us], null, $id);
                        sm_admin(['userinfo_23', $id, $q], ['userinfo_panel']);
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        break;
                    case 'up_coin':
                        user_set_data(['balance[+]' => $q], $id);
                        $us = $db->get('users_information', 'balance', ['user_id' => $id]);
                        $old = $us - $q;
                        $new = $us;
                        insertTransaction('managment', $id, $old, $new, $q, 'increase', $fid);
                        sm_to_user(['up_coin', $q, $us], null, $id);
                        sm_admin(['userinfo_24', $id, $q], ['userinfo_panel']);
                        admin_data(['step' => 'userinfo_2', 'data[JSON]' => ['user_id' => $id]]);
                        break;
                }
            }
            break;
        case 'settings':
            switch ($text) {
                case $key_admin['view_admins']:
                    $result = $db->select('admins', 'user_id');
                    $ids = [];
                    foreach ($result as $row) {
                        $name = $bot->getChatMember($row)['user']['first_name'];
                        $ids[$row] = $name;
                    }
                    $has_admin = in_array($fid, admins);
                    sm_admin(['list_admin', $ids, $has_admin], null);
                    break;
                case $key_admin['sms']:
                    admin_data(['step' => 'settings_edit', 'data' => 'sms_panel']);
                    sm_admin(['sms_panel_1'], ['sms_panel']);
                    break;
                case $key_admin['tikket']:
                    admin_data(['step' => 'settings_edit', 'data' => 'chtiket']);
                    sm_admin(['chtiket_1', $settings['ticket']], ['back_panel']);
                    break;
                case $key_admin['float_number']:
                    admin_data(['step' => 'settings_edit', 'data' => 'chnumber']);
                    sm_admin(['chnumber_1', $settings['number_float']], ['back_panel']);
                    break;
                case $key_admin['edit_spam']:
                    admin_data(['step' => 'settings_edit', 'data' => 'chspam']);
                    sm_admin(['chspam_1'], ['spam_panel']);
                    break;
                case $key_admin['add_admin']:
                    if (in_array($fid, admins)) {
                        admin_data(['step' => 'settings_edit', 'data' => 'addadmin']);
                        sm_admin(['admin_1'], ['back_panel']);
                    }
                    break;
                case $key_admin['del_admin']:
                    if (in_array($fid, admins)) {
                        admin_data(['step' => 'settings_edit', 'data' => 'deladmin']);
                        sm_admin(['admin_1'], ['back_panel']);
                    }
                    break;
                case text_starts_with($text, '/ac_'):
                    $str = str_replace('/ac_', '', $text);
                    if ($str != $fid and $str != admins[0] and in_array($fid, admins)) {
                        $user_status = json_decode($db->get('admins', 'access', ['user_id' => $str]), 1);

                        sm_admin(['access_admin'], ['show_access_admin', $user_status, $str]);
                    } else {
                        sm_admin(['not_access']);
                    }
                    break;
                case $key_admin['DIFF_TIME']:
                    admin_data(['step' => 'settings_edit', 'data' => 'DIFF_TIME']);
                    sm_admin(['DIFF_TIME_1', $settings['DIFF_TIME']], ['back_panel']);
                    break;
                case $key_admin['usd_rate']:
                    admin_data(['step' => 'settings_edit', 'data' => 'usd_rate']);
                    sm_admin(['usd_rate_1', $settings['usd_rate']], ['back_panel']);
                    break;
                case $key_admin['settings']:
                case $key_admin['back_to_settings']:
                    check_allow('settings');
                    admin_step('settings');
                    sm_admin(['settings_1'], ['settings', in_array($fid, admins)]);
                    break;
            }
            break;
        case 'settings_edit':
            if ($text == $key_admin['back_admin_before']) {
                check_allow('settings');
                admin_step('settings');
                sm_admin(['settings_1'], ['settings', in_array($fid, admins)]);
            } else {
                switch ($admin['data']) {
                    case 'sms_panel':
                        switch ($text) {
                            case $key_admin['sms_panel_1']:
                                $value_data = get_option('from_number');
                                admin_data(['step' => 'settings_edit_1', 'data' => 'from_number']);
                                sm_admin(['from_number', $value_data], ['back_panel']);
                                break;
                            case $key_admin['sms_panel_2']:
                                $value_data = get_option('pas_sms');
                                admin_data(['step' => 'settings_edit_1', 'data' => 'pas_sms']);
                                sm_admin(['pas_sms', $value_data], ['back_panel']);
                                break;
                            case $key_admin['sms_panel_3']:
                                $value_data = get_option('user_sms');
                                admin_data(['step' => 'settings_edit_1', 'data' => 'user_sms']);
                                sm_admin(['user_sms', $value_data], ['back_panel']);
                                break;
                            case $key_admin['sms_panel_4']:
                                $value_data = get_option('ptid_ref');
                                admin_data(['step' => 'settings_edit_1', 'data' => 'ptid_ref']);
                                sm_admin(['ptid_ref', $value_data], ['back_panel']);
                                break;
                            case $key_admin['sms_panel_5']:
                                $value_data = get_option('ptid_payment');
                                admin_data(['step' => 'settings_edit_1', 'data' => 'ptid_payment']);
                                sm_admin(['ptid_payment', $value_data], ['back_panel']);
                                break;
                        }
                        break;
                    case 'chspam':
                        switch ($text) {
                            case $key_admin['time_slow_spam']:
                                $value_data = get_option('s_spam');
                                admin_data(['step' => 'settings_edit_1', 'data' => 's_spam']);
                                sm_admin(['s_spam', $value_data], ['back_panel']);
                                break;
                            case $key_admin['time_spam']:
                                $value_data = get_option('s_block');
                                admin_data(['step' => 'settings_edit_1', 'data' => 's_block']);
                                sm_admin(['s_spam', $value_data], ['back_panel']);
                                break;
                        }
                        break;
                    case 'chnumber':
                        if (is_numeric($text)) {
                            admin_step('settings');
                            update_option('number_float', $text);
                            sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        } else {
                            sm_admin(['send_int']);
                        }
                        break;
                    case 'DIFF_TIME':
                        if (is_numeric($text)) {
                            admin_step('settings');
                            update_option('DIFF_TIME', $text);
                            sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        } else {
                            sm_admin(['send_int']);
                        }
                        break;
                    case 'usd_rate':
                        if (is_numeric($text)) {
                            admin_step('settings');
                            update_option('usd_rate', $text);
                            sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        } else {
                            sm_admin(['send_int']);
                        }
                        break;
                    case 'chtiket':
                        if (is_numeric($text)) {
                            admin_step('settings');
                            update_option('ticket', $text);
                            sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        } else {
                            sm_admin(['send_int']);
                        }
                        break;
                    case 'addadmin':
                        if (is_numeric($text)) {
                            if (!$db->has('admins', ['user_id' => $text])) {
                                $user_status = [
                                    "main" => [
                                        "status" => 1,
                                        "sendall" => 0,
                                        "userinfo" => 0,
                                        "settings" => 0,
                                        "apis" => 0,
                                        "products" => 0,
                                        "payments" => 0,
                                        "channels" => 0,
                                        "referral" => 0,
                                        "text" => 0
                                    ],
                                    "sub" => [
                                        "ch_order" => 0,
                                        "support" => 0,
                                        "card" => 0,
                                        "payout" => 0,
                                    ]
                                ];

                                $db->insert('admins', [
                                    'user_id' => $text,
                                    'access[JSON]' => $user_status

                                ]);

                                admin_step('settings');
                                sm_admin(['add_admin', $text], ['settings', in_array($fid, admins)]);
                                sm_admin(['access_admin'], ['show_access_admin', $user_status, $text]);
                                sm_admin(['add_admin_ok'], null, $text);
                            } else {
                                sm_admin(['has_admin']);
                            }
                        } else {
                            sm_admin(['send_int']);
                        }
                        break;
                    case 'deladmin':
                        if (is_numeric($text)) {
                            if ($db->has('admins', ['user_id' => $text])) {
                                $db->delete('admins', ['user_id' => $text]);
                                admin_step('settings');
                                sm_admin(['delete_admin', $text], ['settings', in_array($fid, admins)]);
                                sm_admin(['delete_admin_ok'], null, $text);
                            } else {
                                sm_admin(['hasn_admin']);
                            }
                        }
                        break;
                }
            }
            break;
        case 'settings_edit_1':
            if ($text == $key_admin['back_admin_before']) {
                switch ($admin['data']) {
                    case 'ptid_ref':
                    case 'ptid_payment':
                    case 'user_sms':
                    case 'pas_sms':
                    case 'from_number':
                        admin_data(['step' => 'settings_edit', 'data' => 'sms_panel']);
                        sm_admin(['sms_panel_1'], ['sms_panel']);
                        break;
                    case 's_spam':
                    case 's_block':

                        admin_data(['step' => 'settings_edit', 'data' => 'chspam']);
                        sm_admin(['chspam_1'], ['spam_panel']);

                        break;
                    default:
                        check_allow('settings');
                        admin_step('settings');
                        sm_admin(['settings_1'], ['settings', in_array($fid, admins)]);
                        break;
                }
            } else {
                switch ($admin['data']) {
                    case 'ptid_ref':
                    case 'ptid_payment':
                    case 'user_sms':
                    case 'pas_sms':
                    case 'from_number':
                        admin_step('settings');
                        update_option($admin['data'], $text);
                        sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        break;
                    case 's_spam':
                    case 's_block':
                        if (is_numeric($text)) {
                            admin_step('settings');
                            update_option($admin['data'], $text);
                            sm_admin(['ok_settings_edit', $text], ['settings', in_array($fid, admins)]);
                        }
                }
            }
            break;
        case 'apis':
            switch ($text) {
                case $key_admin['edit_api_setting']:
                    admin_step('edit_api_setting');
                    sm_admin(['edit_api_setting_1', $settings['limit'], $settings['limit_multi']], ['edit_api_setting']);
                    break;
                case $key_admin['edit_api']:
                    $result = $db->select('apis', 'name');
                    if ($result) {
                        admin_step('edit_api');
                        sm_admin(['edit_api'], ['edit_api', $result]);
                    } else {
                        sm_admin(['not_found_api']);
                    }
                    break;
                case $key_admin['add_api']:
                    admin_step('add_api');
                    sm_admin(['add_api_1'], ['back_panel']);
                    break;
                case $key_admin['status_api']:
                    $result = $db->select('apis', '*', ['LIMIT' => 95]);
                    if ($result) {
                        sm_admin(['status_api'], ['status_api', $result]);
                    } else {
                        sm_admin(['not_found_api']);
                    }
                    break;
                case $key_admin['balance_api']:
                    $result = $db->select('apis', '*');
                    if ($result) {
                        sm_admin(['balance_api'], ['balance_api', $result]);
                    } else {
                        sm_admin(['not_found_api']);
                    }
                    break;
            }
            break;
        case 'add_api':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('apis');
                sm_admin(['apis_1'], ['api_panel']);
            } else {
                if (preg_match("/^[a-zA-Z0-9$-\/:-?@{-~!\"^_`\[\]]+$/", $text)) {
                    if (!$db->has('apis', ['name' => $text])) {
                        if ($text !== 'noapi') {
                            if (strlen($text) <= 130) {
                                $admin_data['name'] = $text;
                                admin_data(['step' => "add_api_1", 'data[JSON]' => $admin_data]);
                                sm_admin(['add_api_2'], ['type_api_panel']);
                            } else {
                                sm_admin(['add_api_error_1']);
                            }
                        } else {
                            sm_admin(['add_api_error_2']);
                        }
                    } else {
                        sm_admin(['add_api_error_3']);
                    }
                } else {
                    sm_admin(['add_api_error_4']);
                }
            }
            break;
        case 'add_api_1':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('add_api');
                sm_admin(['add_api_1'], ['back_panel']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                $smart = str_replace([$key_admin['api_type_yes'], $key_admin['api_type_no']], [1, 0], $text);
                $admin_data['smart'] = $smart;
                admin_data(['step' => "add_api_2", 'data[JSON]' => $admin_data]);
                sm_admin(['add_api_3'], ['back_panel']);
            }
            break;
        case 'add_api_2':
            if ($text == $key_admin['back_admin_before']) {
                admin_data(['step' => "add_api_1"]);
                sm_admin(['add_api_2'], ['type_api_panel']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                $admin_data['api_url'] = $text;
                admin_data(['step' => "add_api_3", 'data[JSON]' => $admin_data]);
                sm_admin(['add_api_4'], ['back_panel']);
            }
            break;
        case 'add_api_3':
            if ($text == $key_admin['back_admin_before']) {
                admin_data(['step' => "add_api_2"]);
                sm_admin(['add_api_3'], ['back_panel']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                if ($admin_data['smart'] == 1) {
                    $array = ['smart_panel' => 1, 'name' => $admin_data['name'], 'api_url' => $admin_data['api_url'], 'api_key' => $text];
                    $moj = $api->balance($array);
                    if ($moj['result']) {
                        $db->insert('apis', ['smart_panel' => 1, 'name' => js($admin_data['name']), 'api_url' => $admin_data['api_url'], 'api_key' => $text]);
                        admin_step('apis');
                        sm_admin(['ok_add_api'], ['api_panel']);
                    } else {
                        admin_step('apis');
                        sm_admin(['error_add_api'], ['api_panel']);
                    }
                } elseif ($admin_data['smart'] == 0) {
                    $array = ['smart_panel' => 0, 'name' => $admin_data['name'], 'api_url' => $admin_data['api_url'], 'api_key' => $text];
                    $moj = $api->balance($array);
                    if ($moj['result']) {
                        $db->insert('apis', ['smart_panel' => 0, 'name' => js($admin_data['name']), 'api_url' => $admin_data['api_url'], 'api_key' => $text]);
                        admin_step('apis');
                        sm_admin(['ok_add_api'], ['api_panel']);
                    } else {
                        admin_step('apis');
                        sm_admin(['error_add_api'], ['api_panel']);
                    }
                }
            }
            break;
        case 'edit_api':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('apis');
                sm_admin(['apis_1'], ['api_panel']);
            } else {
                $text_en = js($text);
                $info_api = $db->get('apis', '*', ['name' => $text_en]);
                if ($info_api) {

                    admin_data(['step' => "edit_api_2", 'data[JSON]' => ['id' => $info_api['id']]]);
                    sm_admin(['edit_api_info', $info_api, 0], ['edit_api_update', $info_api['id']]);
                    sm_admin(['edit_api_1'], ['edit_api_panel']);
                } else {
                    sm_admin(['not_found_api']);
                }
            }
            break;
        case 'edit_api_2':
            $admin_data = json_decode($admin['data'], 1);
            switch ($text) {
                case $key_admin['back_admin_before']:
                    $result = $db->select('apis', 'name');
                    if ($result) {
                        admin_step('edit_api');
                        sm_admin(['edit_api'], ['edit_api', $result]);
                    } else {
                        sm_admin(['not_found_api']);
                    }
                    break;
                case $key_admin['api_name']:
                    $admin_data['type'] = 'name';
                    admin_data(['step' => "edit_api_3", 'data[JSON]' => $admin_data]);
                    sm_admin(['new_name_api_name'], ['back_panel']);
                    break;
                case $key_admin['api_url']:
                    $admin_data['type'] = 'url';
                    admin_data(['step' => "edit_api_3", 'data[JSON]' => $admin_data]);
                    sm_admin(['new_url_api_name'], ['back_panel']);

                    break;
                case $key_admin['api_key']:
                    $admin_data['type'] = 'key';
                    admin_data(['step' => "edit_api_3", 'data[JSON]' => $admin_data]);
                    sm_admin(['new_key_api_name'], ['back_panel']);

                    break;
                case $key_admin['api_delete']:
                    $admin_data['type'] = 'delete';
                    admin_data(['step' => "edit_api_3", 'data[JSON]' => $admin_data]);
                    sm_admin(['delete_api_1'], ['ok_admin_panel']);

                    break;
            }
            break;
        case 'edit_api_3':
            $admin_data = json_decode($admin['data'], 1);
            if ($text == $key_admin['back_admin_before']) {
                $info_api = $db->get('apis', '*', ['id' => $admin_data['id']]);
                admin_data(['step' => "edit_api_2", 'data[JSON]' => ['id' => $info_api['id']]]);
                sm_admin(['edit_api_1'], ['edit_api_panel']);
            } else {
                $type = $admin_data['type'];
                switch ($type) {
                    case 'name':
                        if (preg_match("/^[a-zA-Z0-9$-\/:-?@{-~!\"^_`\[\] ]+$/", $text)) {
                            $text_en = js($text);
                            if (!$db->has('apis', ['name' => $text_en])) {
                                if ($text !== 'noapi') {
                                    if (strlen($text) <= 130) {
                                        $info_api = $db->get('apis', '*', ['id' => $admin_data['id']]);
                                        $db->update('products', ['api' => $text_en], ['api' => $info_api['name']]);
                                        $db->update('orders', ['api' => $text_en], ['api' => $info_api['name']]);
                                        $db->update('apis', ['name' => $text, 'name' => $text_en], ['id' => $admin_data['id']]);
                                        admin_data(['step' => "edit_api_2", 'data[JSON]' => ['id' => $info_api['id']]]);
                                        sm_admin(['edit_api_ok'], ['edit_api_panel']);
                                    } else {
                                        sm_admin(['add_api_error_1']);
                                    }
                                } else {
                                    sm_admin(['add_api_error_2']);
                                }
                            } else {
                                sm_admin(['add_api_error_3']);
                            }
                        } else {
                            sm_admin(['add_api_error_4']);
                        }
                        break;
                    case 'url':
                        if (preg_match("/^[a-zA-Z0-9$-\/:-?@{-~!\"^_`\[\] ]+$/", $text)) {

                            $info_api = $db->get('apis', '*', ['id' => $admin_data['id']]);
                            if ($text != $info_api['api_url']) {
                                $info_api['api_url'] = $text;
                                $m = $api->balance($info_api);
                                if ($m['result']) {
                                    $db->update('apis', ['api_url' => $text], ['id' => $admin_data['id']]);
                                    admin_data(['step' => "edit_api_2", 'data[JSON]' => ['id' => $info_api['id']]]);
                                    sm_admin(['edit_api_ok'], ['edit_api_panel']);
                                } else {
                                    sm_admin(['edit_api_error_1']);
                                }
                            } else {
                                sm_admin(['edit_api_error_2']);
                            }
                        } else {
                            sm_admin(['add_api_error_4']);
                        }
                        break;
                    case 'key':
                        if (preg_match("/^[a-zA-Z0-9$-\/:-?@{-~!\"^_`\[\] ]+$/", $text)) {

                            $info_api = $db->get('apis', '*', ['id' => $admin_data['id']]);
                            if ($text != $info_api['api_key']) {
                                $info_api['api_key'] = $text;
                                $m = $api->balance($info_api);
                                if ($m['result']) {
                                    $db->update('apis', ['api_key' => $text], ['id' => $admin_data['id']]);
                                    admin_data(['step' => "edit_api_2", 'data[JSON]' => ['id' => $info_api['id']]]);
                                    sm_admin(['edit_api_ok'], ['edit_api_panel']);
                                } else {
                                    sm_admin(['edit_api_error_3']);
                                }
                            } else {
                                sm_admin(['edit_api_error_4']);
                            }
                        } else {
                            sm_admin(['add_api_error_4']);
                        }
                        break;
                    case 'delete':
                        if ($text == $key_admin['ok_admin']) {
                            $info_api = $db->get('apis', '*', ['id' => $admin_data['id']]);
                            if (!$db->has('orders', ['api' => $info_api['name'], 'status' => ['pending', 'in progress']])) {
                                if ($db->has('products', ['api' => $info_api['name']])) {
                                    $db->delete('apis', ['id' => $admin_data['id']]);
                                    $admin_data['api_name'] = $info_api['name'];
                                    $admin_data['type_del'] = 'product';
                                    admin_data(['step' => "edit_api_4", 'data[JSON]' => $admin_data]);
                                    sm_admin(['delete_api_2'], ['edit_api_delete_1']);
                                } else {
                                    $db->delete('apis', ['id' => $admin_data['id']]);
                                    admin_step('apis');
                                    sm_admin(['delete_api_ok'], ['api_panel']);
                                }
                            } else {
                                $admin_data['api_name'] = $info_api['name'];
                                $admin_data['type_del'] = 'orders';
                                admin_data(['step' => "edit_api_4", 'data[JSON]' => $admin_data]);
                                sm_admin(['delete_api_3'], ['edit_api_delete_2']);
                            }
                        }
                        break;
                }
            }
            break;
        case 'edit_api_4':
            $admin_data = json_decode($admin['data'], 1);
            $type = $admin_data['type'];
            switch ($type) {
                case 'delete':
                    $type_delete = $admin_data['type_del'];
                    switch ($type_delete) {
                        case 'orders':
                            if (in_array($text, array_values($key_admin['api_delete_option']))) {
                                $str = array_search($text, $key_admin['api_delete_option']);
                                switch ($str) {
                                    case 'nothing':
                                        # code...
                                        break;
                                    case 'delete':
                                        $db->delete('orders', ['api' => $admin_data['api_name']]);
                                        break;
                                    case 'completed':
                                        $db->update('orders', ['status' => 'completed'], ['api' => $admin_data['api_name']]);
                                        break;
                                    case 'unknow':
                                        $db->update('orders', ['status' => 'unknow'], ['api' => $admin_data['api_name']]);
                                        break;
                                }

                                if ($db->has('products', ['api' => $admin_data['api_name']])) {
                                    $db->delete('apis', ['id' => $admin_data['id']]);
                                    $admin_data['type_del'] = 'product';
                                    admin_data(['step' => "edit_api_4", 'data[JSON]' => $admin_data]);
                                    sm_admin(['delete_api_4'], ['edit_api_delete_1']);
                                } else {
                                    $db->delete('apis', ['id' => $admin_data['id']]);
                                    admin_step('apis');
                                    sm_admin(['delete_api_ok'], ['api_panel']);
                                }
                            }
                            break;
                        case 'product':
                            if (in_array($text, array_values($key_admin['api_delete_option']))) {
                                $str = array_search($text, $key_admin['api_delete_option']);
                                switch ($str) {
                                    case 'delete':
                                        $db->delete('products', ['api' => $admin_data['api_name']]);
                                        break;
                                    case 'off':
                                        $db->update('products', ['status' => 0], ['api' => $admin_data['api_name']]);
                                        break;
                                    case 'nothing':
                                        # code...
                                        break;
                                }
                                admin_step('apis');
                                sm_admin(['delete_api_ok_prodcut'], ['api_panel']);
                            }
                            break;
                    }
                    break;
            }
            break;
        case 'edit_api_setting':
            switch ($text) {
                case $key_admin['back_admin_before']:
                    admin_step('apis');
                    sm_admin(['apis_1'], ['api_panel']);
                    break;
                case $key_admin['limit']:
                    admin_data(['step' => 'edit_api_setting_2', 'data' => 'limit']);
                    sm_admin(['edit_api_setting_2', $text], ['back_panel']);
                    break;
                case $key_admin['limit_multi']:
                    admin_data(['step' => 'edit_api_setting_2', 'data' => 'limit_multi']);
                    sm_admin(['edit_api_setting_2', $text], ['back_panel']);
                    break;
            }
            break;
        case 'edit_api_setting_2':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('edit_api_setting');
                sm_admin(['edit_api_setting_1', $settings['limit'], $settings['limit_multi']], ['edit_api_setting']);
            } else {
                if (is_numeric($text) and $text > 0) {
                    if ($admin['data'] == 'limit') {
                        if ($text <= 300) {
                            $settings['limit'] = $text;
                            update_option('limit', $text);
                        }
                    } elseif ($admin['data'] == 'limit_multi') {
                        if ($text <= 100) {
                            $settings['limit_multi'] = $text;
                            update_option('limit_multi', $text);
                        }
                    }
                    admin_step('edit_api_setting');
                    sm_admin(['edit_api_setting_ok', $settings['limit'], $settings['limit_multi']], ['edit_api_setting']);
                }
            }

            break;
        case 'payments':
            switch ($text) {
                case $key_admin['payment_status']:
                    $result = $db->select('payment_gateways', '*');
                    if ($result) {
                        sm_admin(['payment_status'], ['payments_status', $result]);
                    } else {
                        sm_admin(['not_payment']);
                    }
                    break;
                case $key_admin['payment_edit']:
                    $result = $db->select('payment_gateways', 'name');
                    if ($result) {
                        admin_step('edit_payment');
                        sm_admin(['edit_payment'], ['edit_payment', $result]);
                    } else {
                        sm_admin(['not_payment']);
                    }
                    break;
                case $key_admin['payment_add']:
                    admin_step('add_payment');
                    sm_admin(['add_payment_1'], ['back_panel']);
                    break;
                case $key_admin['payment_discount']:
                    admin_step('payment_discount');
                    sm_admin(['payment_discount'], ['payment_discount']);
                    break;
                case $key_admin['payment_edit_setting']:
                    admin_step('payment_edit_setting');
                    sm_admin(['payment_edit_setting'], ['payment_option']);
                    break;
            }
            break;
        case 'add_payment':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payments');
                sm_admin(['payments_1'], ['payment_key']);
            } else {
                if (!$db->has('payment_gateways', ['name' => $text])) {
                    if (strlen($text) <= 130) {
                        admin_data(['step' => 'add_payment_2', 'data[JSON]' => ['name' => $text]]);
                        $result = array_diff(scandir(ROOTPATH . '/payment'), ['.', '..', 'index.php', 'error_log', '.htaccess', 'show.php']);
                        sm_admin(['add_payment_2'], ['payment_file', $result, $db]);
                    } else {
                        sm_admin(['error_add_payment_1']);
                    }
                } else {
                    sm_admin(['error_add_payment_2', $text]);
                }
            }
            break;
        case 'add_payment_2':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                admin_step('add_payment');
                sm_admin(['add_payment_1'], ['back_panel']);
            } else {
                if (!$db->has('payment_gateways', ['file' => $text])) {
                    $admin_data['file'] = $text;
                    admin_data(['step' => 'add_payment_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['add_payment_3'], ['back_panel']);
                } else {
                    sm_admin(['error_add_payment_3', $text]);
                }
            }
            break;
        case 'add_payment_3':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                admin_step('add_payment_2');
                $result = array_diff(scandir(ROOTPATH . '/payment'), ['.', '..', 'index.php', 'error_log', '.htaccess']);
                sm_admin(['add_payment_2'], ['payment_file', $result, $db]);
            } else {
                $insert_data = ['name' => $admin_data['name'], 'file' => $admin_data['file'], 'code' => $text];
                $db->insert('payment_gateways', $insert_data);
                $result = $db->id();
                if ($result) {
                    admin_step('payments');
                    sm_admin(['add_payment_ok'], ['payment_key']);
                } else {
                    sm_admin(['error_add_payment_4']);
                }
            }
            break;
        case 'edit_payment':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payments');
                sm_admin(['payments_1'], ['payment_key']);
            } else {
                $info_payment = $db->get('payment_gateways', '*', ['name' => $text]);
                if ($info_payment) {
                    admin_data(['step' => "edit_payment_2", 'data[JSON]' => ['id' => $info_payment['id']]]);
                    sm_admin(['edit_payment_1', $info_payment], ['payment_edit_key']);
                } else {
                    sm_admin(['error_edit_payment_1', $text]);
                }
            }
            break;
        case 'edit_payment_2':
            if ($text == $key_admin['back_admin_before']) {
                $result = $db->select('payment_gateways', 'name');
                if ($result) {
                    admin_step('edit_payment');
                    sm_admin(['edit_payment'], ['edit_payment', $result]);
                } else {
                    sm_admin(['not_payment']);
                }
            } else {
                $admin_data = json_decode($admin['data'], true);
                switch ($text) {
                    case $key_admin['payment_edit_1']:
                        # Name
                        $admin_data['type'] = 'name';
                        admin_data(['step' => "edit_payment_3", 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_payment_2'], ['back_panel']);
                        break;
                    case $key_admin['payment_edit_2']:
                        # Merchent
                        $admin_data['type'] = 'code';
                        admin_data(['step' => "edit_payment_3", 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_payment_3'], ['back_panel']);
                        break;
                    case $key_admin['payment_edit_3']:
                        # Delete
                        $admin_data['type'] = 'delete';
                        admin_data(['step' => "edit_payment_3", 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_payment_4'], ['ok_cancel_admin_panel']);
                        break;
                }
            }
            break;
        case 'edit_payment_3':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $info_payment = $db->get('payment_gateways', '*', ['id' => $admin_data['id']]);
                if ($info_payment) {
                    admin_data(['step' => "edit_payment_2", 'data[JSON]' => ['id' => $info_payment['id']]]);
                    sm_admin(['edit_payment_1', $info_payment], ['payment_edit_key']);
                }
            } else {
                switch ($admin_data['type']) {
                    case 'name':

                        if (!$db->has('payment_gateways', ['name' => $text])) {
                            if (strlen($text) <= 130) {

                                $db->update('payment_gateways', ['name' => $text], ['id' => $admin_data['id']]);
                                admin_data(['step' => "edit_payment_2", 'data[JSON]' => ['id' => $admin_data['id']]]);
                                sm_admin(['ok_payment_edit'], ['payment_edit_key']);
                            } else {
                                sm_admin(['error_edit_payment_2']);
                            }
                        } else {
                            sm_admin(['error_edit_payment_3']);
                        }
                        break;
                    case 'code':
                        $db->update('payment_gateways', ['code' => $text], ['id' => $admin_data['id']]);
                        admin_data(['step' => "edit_payment_2", 'data[JSON]' => ['id' => $admin_data['id']]]);
                        sm_admin(['ok_payment_edit'], ['payment_edit_key']);
                        break;
                    case 'delete':
                        if ($text == $key_admin['ok_admin']) {
                            $db->delete('payment_gateways', ['id' => $admin_data['id']]);
                            admin_step('payments');
                            sm_admin(['ok_payment_delete'], ['payment_key']);
                        }
                        break;
                }
            }
            break;
        case 'payment_edit_setting':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payments');
                sm_admin(['payments_1'], ['payment_key']);
            } else {
                if (in_array($text, array_values($key_admin['payment_option']))) {
                    $str = array_search($text, $key_admin['payment_option']);
                    admin_data(['step' => 'payment_edit_setting_2', 'data[JSON]' => ['type' => $str]]);
                    sm_admin(['payment_edit_setting_2', $settings[$str]], ['back_panel']);
                }
            }
            break;
        case 'payment_discount':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payments');
                sm_admin(['payments_1'], ['payment_key']);
            } else {
                switch ($text) {
                    case $key_admin['add_code_discount']:
                        admin_step('cr_code');
                        sm_admin(['cr_code_1'], ['back_panel']);
                        break;
                    case $key_admin['edit_code_discount']:
                        $result = $db->select('gift_code', 'code', ['LIMIT' => 90, 'ORDER' => ['id' => 'ASC'], 'status' => 1]);
                        if ($result) {
                            admin_step('edit_code');
                            sm_admin(['edit_code_1'], ['edit_code', $result]);
                        } else {
                            sm_admin(['edit_code_error_1']);
                        }
                        break;
                }
            }
            break;
        case 'cr_code':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payments');
                sm_admin(['payments_1'], ['payment_key']);
                return;
            }
            if (preg_match("/^[a-zA-Z0-9$-\/:-?@{-~!\"^_`\[\]]+$/", $text)) {
                if ($db->has('gift_code', ['code' => $text, 'status' => 1])) {
                    sm_admin(['edit_code_error_2']);
                } else {
                    admin_data(['step' => 'cr_code_2', 'data[JSON]' => ['code' => trim($text)]]);
                    sm_admin(['cr_code_2'], ['select_type_code']);
                }
            } else {
                sm_admin(['edit_code_error_3']);
            }
            break;
        case 'cr_code_2':
            $admin_data = json_decode($admin['data'], 1);
            switch ($text) {
                case $key_admin['percent_code']:
                    $admin_data['type'] = 'percent';
                    admin_data(['step' => 'cr_code_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['cr_code_3'], ['back_panel']);
                    break;
                case $key_admin['once_code']:
                    $admin_data['type'] = 'fix';
                    admin_data(['step' => 'cr_code_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['cr_code_4'], ['back_panel']);
                    break;
            }
            break;
        case 'cr_code_3':
            $admin_data = json_decode($admin['data'], 1);
            $ex = explode("\n", $text);
            $c = count($ex);
            $type = $admin_data['type'];
            switch ($type) {
                case 'percent':
                    if ($c == 3) {
                        $type = 'percent';
                        $amount = $ex[0];
                        $max = $ex[1];
                        $count = $ex[2];
                        if (is_numeric($amount) and is_numeric($max) and is_numeric($count)) {
                            if ($amount > 0 and $max > 0 and $count > 0) {
                                $db->insert('gift_code', ['code' => $admin_data['code'], 'type' => $type, 'count' => $count, 'amount[JSON]' => ['amount' => $amount, 'max' => $max], 'status' => 1, 'ids[JSON]' => []]);
                                if ($db->id()) {
                                    admin_step('payments');
                                    sm_admin(['ok_add_discount_code'], ['payment_key']);
                                }
                            } else {
                                sm_admin(['edit_code_error_4']);
                            }
                        } else {
                            sm_admin(['edit_code_error_5']);
                        }
                    } else {
                        sm_admin(['edit_code_error_6']);
                    }
                    break;
                case 'fix':
                    if ($c == 2) {
                        $type = 'fix';
                        $amount = $ex[0];
                        $max = $amount;
                        $count = $ex[1];
                        if (is_numeric($amount) and is_numeric($max) and is_numeric($count)) {
                            if ($amount > 0 and $max > 0 and $count > 0) {
                                $db->insert('gift_code', ['code' => $admin_data['code'], 'type' => $type, 'count' => $count, 'amount[JSON]' => ['amount' => $amount, 'max' => $max], 'status' => 1, 'ids[JSON]' => []]);
                                if ($db->id()) {
                                    admin_step('payments');
                                    sm_admin(['ok_add_discount_code'], ['payment_key']);
                                }
                            } else {
                                sm_admin(['edit_code_error_7']);
                            }
                        } else {
                            sm_admin(['edit_code_error_8']);
                        }
                    } else {
                        sm_admin(['edit_code_error_9']);
                    }
                    break;
            }
            break;
        case 'edit_code':
            $result = $db->get('gift_code', '*', ['code' => $text, 'status' => 1]);
            if ($result) {
                admin_data(['step' => "edit_code_2", 'data[JSON]' => ['id' => $result['id']]]);
                sm_admin(['edit_code_2', $result], ['edit_code_panel']);
            } else {
                sm_admin(['edit_code_error_10']);
            }
            break;
        case 'edit_code_2':
            $admin_data = json_decode($admin['data'], 1);
            if ($text == $key_admin['back_admin_before']) {
                $result = $db->select('gift_code', 'code', ['LIMIT' => 90, 'ORDER' => ['id' => 'ASC'], 'status' => 1]);
                if ($result) {
                    admin_step('edit_code');
                    sm_admin(['edit_code_1'], ['edit_code', $result]);
                } else {
                    sm_admin(['edit_code_error_1']);
                }
            } else {
                if (in_array($text, array_values($key_admin['edit_discount_panel']))) {
                    $str = array_search($text, $key_admin['edit_discount_panel']);
                    $result = $db->get('gift_code', '*', ['id' => $admin_data['id']]);
                    switch ($str) {
                        case 'delete_code':
                            $admin_data['type'] = $str;
                            admin_data(['step' => "edit_code_3", 'data[JSON]' => $admin_data]);
                            sm_admin(['edit_code_3'], ['ok_admin_panel']);
                            break;
                        case 'max_discount':
                            if ($result['type'] == 'percent') {
                                $admin_data['type'] = $str;
                                admin_data(['step' => "edit_code_3", 'data[JSON]' => $admin_data]);
                                sm_admin(['edit_code_4'], ['back_panel']);
                            } elseif ($result['type'] == 'fix') {
                                // error
                                sm_admin(['cant_edit_code']);
                            }
                            break;
                        case 'amount_code':
                        case 'count_code':
                            $admin_data['type'] = $str;
                            admin_data(['step' => "edit_code_3", 'data[JSON]' => $admin_data]);
                            sm_admin(['edit_code_4'], ['back_panel']);
                            break;
                    }
                }
            }
            break;
        case 'edit_code_3':
            $admin_data = json_decode($admin['data'], 1);
            $result = $db->get('gift_code', '*', ['id' => $admin_data['id'], 'status' => 1]);
            if ($text == $key_admin['back_admin_before']) {
                if ($result) {
                    admin_data(['step' => "edit_code_2", 'data[JSON]' => ['id' => $result['id']]]);
                    sm_admin(['edit_code_2', $result], ['edit_code_panel']);
                } else {
                    sm_admin(['edit_code_error_10']);
                }
            } else {
                $type = $admin_data['type'];
                switch ($type) {
                    case 'delete_code':
                        if ($text == $key_admin['ok_admin']) {
                            $db->delete('gift_code', ['id' => $admin_data['id']]);
                            admin_step('payments');
                            sm_admin(['ok_edit_code'], ['payment_key']);
                        }
                        break;
                    case 'max_discount':
                        $result['max_amount'] = $text;
                        $db->update('gift_code', ['max_amount' => $text], ['id' => $admin_data['id']]);
                        admin_data(['step' => "edit_code_2", 'data[JSON]' => ['id' => $result['id']]]);
                        sm_admin(['edit_code_2', $result], ['edit_code_panel']);
                        break;
                    case 'amount_code':
                        $result['amount'] = $text;
                        $db->update('gift_code', ['amount' => $text], ['id' => $admin_data['id']]);
                        admin_data(['step' => "edit_code_2", 'data[JSON]' => ['id' => $result['id']]]);
                        sm_admin(['edit_code_2', $result], ['edit_code_panel']);
                        break;
                    case 'count_code':
                        $result['count'] = $text;
                        $db->update('gift_code', ['count' => $text], ['id' => $admin_data['id']]);
                        admin_data(['step' => "edit_code_2", 'data[JSON]' => ['id' => $result['id']]]);
                        sm_admin(['edit_code_2', $result], ['edit_code_panel']);
                        break;
                }
            }
            break;
        case 'payment_edit_setting_2':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('payment_edit_setting');
                sm_admin(['payment_edit_setting'], ['payment_option']);
            } else {
                $admin_data = json_decode($admin['data'], true);
                if (is_numeric($text) && $text > 0) {
                    update_option($admin_data['type'], $text);
                    admin_step('payment_edit_setting');
                    sm_admin(['ok_payment_edit_setting'], ['payment_option']);
                }
            }
            break;
        case 'channels':
            if (in_array($text, array_values($key_admin['channels_key']))) {
                $str = array_search($text, $key_admin['channels_key']);
                admin_data(['step' => 'ch_channel_2', 'data[JSON]' => ['en' => $str, 'fa' => $text]]);
                $channel = get_option($str, 0);
                if ($channel != 0) {
                    if ($str == 'channel_main' or $str == 'channel_lock') {
                        $tx = '@' . $channel;
                        $g = $bot->bot('GetChat', ['chat_id' => $tx]);
                        if ($g['result']['type'] == 'channel') {
                            $name = $g['result']['title'];
                        } else {
                            $name = null;
                        }
                    } else {

                        $tx = $channel;
                        $g = $bot->bot('GetChat', ['chat_id' => $channel]);
                        if ($g['result']['type'] == 'channel') {
                            $name = $g['result']['title'];
                        } else {
                            $name = null;
                        }
                    }
                } else {
                    $tx = $media_admin->atext('error_channel_1');
                    $name = null;
                }
                sm_admin(['channel_edit_1', $text, $tx, $name], ['back_panel']);
            } else {
                sm_admin(['error_channel_2']);
            }
            break;
        case 'ch_channel_2':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('channels');
                sm_admin(['channels_1'], ['channel_key']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                if ($admin_data['en'] == 'channel_main' or $admin_data['en'] == 'channel_lock') {
                    if (isset($forward_from_chat) and $for_type == 'channel') {
                        @$check = $bot->check_join($numberId, $for_id);
                        if ($check == 'administrator') {
                            update_option($admin_data['en'], $for_user_name);
                            admin_step('channels');
                            sm_admin(['ok_edit_channel', '@' . $for_user_name, $admin_data['fa']], ['channel_key']);
                        } else {
                            sm_admin(['error_channel_3']);
                        }
                    } else {
                        sm_admin(['error_channel_4']);
                    }
                } else {
                    if (isset($forward_from_chat) and $for_type == 'channel') {
                        @$check = $bot->check_join($numberId, $for_id);
                        if ($check == 'administrator') {
                            update_option($admin_data['en'], $for_id);
                            admin_step('channels');
                            sm_admin(['ok_edit_channel', $for_id, $admin_data['fa']], ['channel_key']);
                        } else {
                            sm_admin(['error_channel_3']);
                        }
                    } elseif (is_numeric($text)) {
                        update_option($admin_data['en'], $text);
                        admin_step('channels');
                        sm_admin(['ok_edit_channel', $text, $admin_data['fa']], ['channel_key']);
                    } else {
                        sm_admin(['error_channel_4']);
                    }
                }
            }
            break;
        case 'text':
            if (in_array($text, array_values($key_admin['text_key']))) {
                $str = array_search($text, $key_admin['text_key']);
                admin_data(['step' => 'edit_text', 'data[JSON]' => ['en' => $str, 'fa' => $text]]);
                $tx = get_option($str, 0);
                sm_admin(['edit_text_1', $tx], ['back_panel']);
            } else {
                sm_admin(['error_text_1']);
            }
            break;
        case 'edit_text':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('text');
                sm_admin(['text_1'], ['text_key']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                update_option($admin_data['en'], $text);
                admin_step('text');
                sm_admin(['ok_edit_text', $text, $admin_data['fa']], ['text_key']);
            }
            # code...
            break;
        case 'referral':
            if (in_array($text, array_values($key_admin['referral_key']))) {
                $str = array_search($text, $key_admin['referral_key']);
                admin_data(['step' => 'edit_referral', 'data[JSON]' => ['en' => $str, 'fa' => $text]]);

                switch ($str) {
                    case 'baner_tx':
                        $tx = get_option($str, 0);
                        sm_admin(['edit_gift_1', $tx], ['back_panel']);
                        break;
                    case 'baner_photo':
                        $bot->bot('sendphoto', [
                            'chat_id' => $fid,
                            'photo' => new CURLFile(ROOTPATH . "/baner.jpg"),
                        ]);
                        sm_admin(['edit_gift_2'], ['back_panel']);
                        break;
                    case 'gift_referral':
                    case 'gift_payment':
                    case 'gift_start':
                    case 'min_payment_gift':
                    case 'min_move_gift':
                        $tx = get_option($str, 0);
                        sm_admin(['edit_gift_3', $tx], ['back_panel']);
                        break;
                }
            } else {
                sm_admin(['error_referral_1']);
            }
            break;
        case 'edit_referral':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('referral');
                sm_admin(['referral_1'], ['referral_key']);
            } else {
                $admin_data = json_decode($admin['data'], 1);
                $type = $admin_data['en'];
                switch ($type) {
                    case 'baner_tx':
                        update_option($admin_data['en'], $text);
                        admin_step('referral');
                        sm_admin(['ok_edit_gift'], ['referral_key']);
                        break;
                    case 'baner_photo':
                        if (isset($update['message']['photo'])) {
                            $photo = $update['message']['photo'];
                            $file = end($photo)['file_id'];
                            $get = $bot->bot('getfile', ['file_id' => $file]);
                            if ($get['ok']) {
                                $patch = $get['result']['file_path'];
                                @unlink('baner.jpg');
                                file_put_contents(ROOTPATH . '/baner.jpg', curl_get('https://api.telegram.org/file/bot' . Token . '/' . $patch));
                                admin_step('referral');
                                sm_admin(['ok_edit_gift'], ['referral_key']);
                            }
                        }
                        break;
                    case 'gift_referral':
                    case 'gift_payment':
                    case 'gift_start':
                    case 'min_payment_gift':
                    case 'min_move_gift':
                        if (is_numeric($text) and $text >= 0) {
                            update_option($admin_data['en'], $text);
                            admin_step('referral');
                            sm_admin(['ok_edit_gift'], ['referral_key']);
                        }
                        break;
                }
            }
            break;
        case 'products':
            switch ($text) {
                case $key_admin['add_product']:
                    admin_step('add_product');
                    sm_admin(['add_product_1'], ['type_add_product']);
                    break;
                case $key_admin['edit_product']:
                    $result = get_category(['offset' => 0, 'status' => 1], null);
                    if ($result) {
                        $c = $db->count('categories', ['category_id' => null]);
                        $admin_data = ['offset_main' => 0];
                        admin_data(['step' => 'edit_1', 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_shop_1'], ['category_select_panel', $result, $c, null, 0]);
                    } else {
                        sm_admin(['edit_shop_error_1']);
                    }

                    break;
                case $key_admin['delete_product']:
                    admin_step('delete_all');
                    sm_admin(['type_of_delete'], ['type_of_delete']);
                    break;
                case $key_admin['update_product']:

                    $result = $db->select('apis', 'name', ['smart_panel' => 1]);
                    if ($result) {
                        admin_step('update_api_1');
                        sm_admin(['update_api_1'], ['api_select_panel', $result, false]);
                    } else {
                        sm_admin(['update_api_error_1']);
                    }
                    break;
                case $key_admin['status_product']:
                    $result = get_category(['inline', 'offset' => 0, 'status' => 1], null);
                    if ($result) {
                        $c = $db->count('categories', ['category_id' => null]);
                        sm_admin(['product_status'], ['category_status', $result, $c, 0, 0]);
                    } else {
                        sm_admin(['product_status_error_1']);
                    }

                    break;
                case $key_admin['display_product']:
                    sm_admin(['display_product'], ['display_prodcuts']);
                    break;
            }
            break;
        case 'display_product_1':
            $admin_data = json_decode($admin['data'], true);
            $type = $admin_data['type'];
            $text = convertnumber($text);
            switch ($type) {
                case 'row_product':
                    $explode = explode('-', $text);
                    $isValid = true;
                    foreach ($explode as $num) {
                        if (!is_numeric($num)) {
                            $isValid = false;
                            break;
                        }
                    }
                    if (count($explode) > 0 && $isValid) {
                        $category = json_decode($settings['display_products'], true);
                        $category['row'] = $explode;
                        update_option('display_products', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
                case 'row_under':
                    $explode = explode('-', $text);
                    $isValid = true;
                    foreach ($explode as $num) {
                        if (!is_numeric($num)) {
                            $isValid = false;
                            break;
                        }
                    }
                    if (count($explode) > 0 && $isValid) {
                        $category = json_decode($settings['display_sub_category'], true);
                        $category['row'] = $explode;
                        update_option('display_sub_category', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
                case 'row_category':
                    $explode = explode('-', $text);
                    $isValid = true;
                    foreach ($explode as $num) {
                        if (!is_numeric($num)) {
                            $isValid = false;
                            break;
                        }
                    }
                    if (count($explode) > 0 && $isValid) {
                        $category = json_decode($settings['display_category'], true);
                        $category['row'] = $explode;
                        update_option('display_category', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
                case 'page_product':
                    if (is_numeric($text) && $text >= 1) {
                        $category = json_decode($settings['display_products'], true);
                        $category['page'] = $text;
                        update_option('display_products', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
                case 'page_under':
                    if (is_numeric($text) && $text >= 1) {
                        $category = json_decode($settings['display_sub_category'], true);
                        $category['page'] = $text;
                        update_option('display_sub_category', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
                case 'page_category':
                    if (is_numeric($text) && $text >= 1) {
                        $category = json_decode($settings['display_category'], true);
                        $category['page'] = $text;
                        update_option('display_category', js($category));
                        admin_step('products');
                        $bot->delete_msg($fid, $message_id);
                        edt_admin(['display_product'], ['display_prodcuts', true], $admin_data['msgid']);
                    } else {
                        sm_admin(['error_display_format']);
                    }
                    break;
            }
            break;


        case 'add_product':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('products');
                sm_admin(['products_1'], ['products_panel']);
            } else {
                if (in_array($text, array_values($key_admin['product_type']))) {
                    $str = array_search($text, $key_admin['product_type']);
                    switch ($str) {
                        case 'category':
                            $admin_data = ['type' => $str];
                            admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                            sm_admin(['category_add_1'], ['back_panel']);
                            break;
                        case 'sub_category':
                            $result = get_category(['offset' => 0, 'status' => 1], 0);
                            if ($result) {
                                $admin_data = ['offset' => 0, 'type' => $str];
                                $c = $db->count('categories', 'id', ['category_id' => null]);
                                admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                                sm_admin(['sub_category_add_1'], ['category_select_panel', $result, $c, null, 0]);
                            } else {

                                sm_admin(['edit_shop_error_2']);
                            }
                            break;
                        case 'product':
                            $result = get_category(['offset' => 0, 'status' => 1], 0);
                            if ($result) {
                                $admin_data = ['offset_main' => 0, 'type' => $str];
                                $c = $db->count('categories', 'id', ['category_id' => null]);
                                admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                                sm_admin(['product_add_1'], ['category_select_panel', $result, $c, null, 0]);
                            } else {
                                sm_admin(['edit_shop_error_2']);
                            }
                            break;
                    }
                }
            }
            break;
        case 'add_product_2':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                admin_step('add_product');
                sm_admin(['add_product_1'], ['type_add_product']);
            } else {
                $text = removeWhiteSpace($text);
                $text_en = js($text);
                switch ($admin_data['type']) {
                    case 'category':

                        if (!$db->has('categories', ['name' => $text_en])) {
                            $ordering = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                            $ordering += 1;
                            $in_data = [
                                'name' => $text_en,
                                'category_id' => null,
                                'status' => 1,
                                'ordering' => $ordering,
                            ];
                            $db->insert('categories', $in_data);
                            admin_data(['step' => 'add_product_2', 'data[JSON]' => ['type' => $admin_data['type']]]);
                            sm_admin(['category_add_2'], ['back_panel']);
                        } else {
                            sm_admin(['category_add_error_1']);
                        }
                        break;
                    case 'sub_category':
                        if ($text == $key_admin['next_page'] or $text == $key_admin['prev_page']) {

                            $displaySettings = json_decode($settings['display_category'], true);
                            $now = $admin_data['offset'];

                            if ($text == $key_admin['next_page']) {
                                $str = $now + $displaySettings['page'];
                            } else {
                                $str = $now - $displaySettings['page'];
                            }

                            $result = get_category(['offset' => $str, 'status' => 1], 0);
                            if ($result) {
                                $c = $db->count('categories', 'id', ['category_id' => null]);
                                $admin_data['offset'] = $str;
                                admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                                sm_admin(['sub_category_add_1'], ['category_select_panel', $result, $c, null, $str]);
                            } else {
                                sm_admin(['category_add_error_2']);
                            }
                        } else {
                            $get = $db->get('categories', 'id', ['name' => js($text)]);
                            if ($get) {
                                if ($db->has('products', ['category_id' => $get])) {
                                    sm_admin(['category_add_error_3']);
                                } else {
                                    $admin_data['category_id'] = $get;
                                    admin_data(['step' => 'add_product_3', 'data[JSON]' => $admin_data]);
                                    sm_admin(['sub_category_add_2'], ['back_panel']);
                                }
                            } else {
                                sm_admin(['category_add_error_4']);
                            }
                        }
                        break;
                    case 'product':
                        if ($text == $key_admin['next_page'] or $text == $key_admin['prev_page']) {

                            $displaySettings = json_decode($settings['display_category'], true);
                            $now = $admin_data['offset_main'];

                            if ($text == $key_admin['next_page']) {
                                $str = $now + $displaySettings['page'];
                            } else {
                                $str = $now - $displaySettings['page'];
                            }

                            $result = get_category(['offset' => $str, 'status' => 1], 0);
                            if ($result) {
                                $c = $db->count('categories', 'id', ['category_id' => null]);
                                $admin_data['offset_main'] = $str;
                                admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                                sm_admin(['product_add_1'], ['category_select_panel', $result, $c, null, $str]);
                            } else {
                                sm_admin(['category_add_error_5']);
                            }
                        } else {
                            $get = $db->get('categories', 'id', ['name' => js($text)]);
                            if ($get) {
                                $result = get_category(['offset' => 0, 'status' => 1], $get);
                                if ($result) {
                                    $c = $db->count('categories', 'id', ['category_id' => $get]);
                                    $admin_data['category_id'] = $get;
                                    $admin_data['offset_under'] = 0;
                                    admin_data(['step' => 'add_product_3', 'data[JSON]' => $admin_data]);
                                    sm_admin(['product_add_2'], ['category_select_panel', $result, $c, $get, 0]);
                                } else {
                                    // Not Have Under Category
                                    $admin_data['category_id'] = $get;
                                    $result = $db->select('apis', 'name', ['LIMIT' => 95]);
                                    sm_admin(['product_add_3'], ['product_add_api', $result]);
                                    admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);
                                }
                            } else {
                                sm_admin(['category_add_error_4']);
                            }
                        }
                        break;
                }
            }
            break;
        case 'add_product_3':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                switch ($admin_data['type']) {
                    case 'sub_category':
                        $str = $admin_data['offset'];
                        $result = get_category(['offset' => $str, 'status' => 1], 0);
                        if ($result) {
                            $c = $db->count('categories', 'id', ['category_id' => null]);
                            $admin_data['offset'] = $str;
                            admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                            sm_admin(['sub_category_add_1'], ['category_select_panel', $result, $c, null, $str]);
                        } else {
                            sm_admin(['category_add_error_2']);
                        }
                        break;
                    case 'product':
                        $current_category = $db->get('categories', ['category_id'], ['id' => $admin_data['category_id']]);

                        if ($current_category && $current_category['category_id'] !== null) {
                            $result = get_category(['offset' => 0, 'status' => 1], $current_category['category_id']);

                            $c = $db->count('categories', 'id', ['category_id' => $current_category['category_id']]);
                            admin_data(['step' => 'add_product_3', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_2'], ['category_select_panel', $result, $c, $current_category['category_id'], 0]);
                        } else {
                            $result = get_category(['offset' => 0, 'status' => 1], 0);
                            $c = $db->count('categories', 'id', ['category_id' => null]);
                            admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_1'], ['category_select_panel', $result, $c, null, 0]);
                        }
                        break;
                }
            } else {
                $text = removeWhiteSpace($text);
                $text_en = js($text);
                switch ($admin_data['type']) {
                    case 'sub_category':
                        if (!$db->has('categories', ['name' => $text_en, 'category_id' => $admin_data['category_id']])) {
                            $ordering = (int) $db->max('categories', 'ordering', ['category_id' => $admin_data['category_id']]);
                            $ordering += 1;
                            $in_data = [
                                'name' => $text_en,
                                'category_id' => $admin_data['category_id'],
                                'status' => 1,
                                'ordering' => $ordering,
                            ];
                            $db->insert('categories', $in_data);
                            admin_data(['step' => 'add_product_3']);
                            sm_admin(['sub_category_add_3'], ['back_panel']);
                        } else {
                            sm_admin(['category_add_error_1']);
                        }
                        break;
                    case 'product':
                        $get = $db->get('categories', 'id', ['name' => js($text)]);
                        if ($get) {

                            $admin_data['category_id'] = $get;

                            $result = $db->select('apis', 'name', ['LIMIT' => 95]);
                            admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_3'], ['product_add_api', $result]);
                        } else {
                            sm_admin(['category_add_error_4']);
                        }
                        break;
                }
            }
            break;
        case 'add_product_4':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $category_id = $admin_data['category_id'];

                $current_category = $db->get('categories', ['category_id'], ['id' => $category_id]);

                if ($current_category && $current_category['category_id'] !== null) {
                    $result = get_category(['offset' => 0, 'status' => 1], $current_category['category_id']);
                    $c = $db->count('categories', 'id', ['category_id' => $current_category['category_id']]);
                    admin_data(['step' => 'add_product_3', 'data[JSON]' => $admin_data]);
                    sm_admin(['product_add_2'], ['category_select_panel', $result, $c, $current_category['category_id'], 0]);
                } else {
                    $result = get_category(['offset' => 0, 'status' => 1], 0);
                    $c = $db->count('categories', 'id', ['category_id' => null]);
                    admin_data(['step' => 'add_product_2', 'data[JSON]' => $admin_data]);
                    sm_admin(['product_add_1'], ['category_select_panel', $result, $c, null, 0]);
                }
            } else {
                switch ($admin_data['type']) {
                    case 'product':
                        if ($text == $key_admin['no_api']) {
                            $admin_data['api'] = 0;
                            admin_data(['step' => 'add_product_5', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_4', 0], ['back_panel']);
                        } else {
                            $result_api = $db->get('apis', '*', ['name' => js($text)]);
                            if ($result_api) {
                                if ($result_api['smart_panel']) {
                                    $admin_data['api'] = $result_api['id'];
                                    admin_data(['step' => 'add_product_5', 'data[JSON]' => $admin_data]);
                                    sm_admin(['product_add_4', 1], ['back_panel']);
                                } else {
                                    $admin_data['api'] = $result_api['id'];
                                    admin_data(['step' => 'add_product_5', 'data[JSON]' => $admin_data]);
                                    sm_admin(['product_add_4', 2], ['back_panel']);
                                }
                            } else {
                                sm_admin(['product_add_bot_key']);
                            }
                        }
                        break;
                }
            }
            break;
        case 'add_product_5':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {

                $result = $db->select('apis', 'name', ['LIMIT' => 95]);
                admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);
                sm_admin(['product_add_3'], ['product_add_api', $result]);
            } else {
                $explode = explode("\n", $text);
                if ($admin_data['api'] == 0) {
                    if (count($explode) == 4) {
                        $name_product = removeWhiteSpace($explode[0]);
                        $name_en = json_encode($name_product);
                        $price = trim($explode[1]);
                        $min = trim($explode[2]);
                        $max = trim($explode[3]);
                        if (mb_strlen($name_product) <= 130) {
                            if (is_numeric($price) and is_numeric($min) and is_numeric($max) and $min > 0 and $max > 0) {
                                $btn = $db->get('categories', '*', ['id' => $admin_data['category_id']]);
                                if (!$db->has('products', ['name' => $name_en, 'category_id' => $btn['id']])) {
                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $btn['id']]);
                                    $ordering += 1;
                                    $db->insert('products', [
                                        'name' => $name_en,
                                        'price' => $price,
                                        'min' => $min,
                                        'max' => $max,
                                        'info' => null,
                                        'api' => 'noapi',
                                        'service' => 0,
                                        'category_id' => $btn['id'],
                                        'ordering' => $ordering,
                                    ]);
                                    $insert = $db->id();
                                    if ($insert) {
                                        $admin_data['id'] = $insert;
                                        admin_data(['step' => 'add_product_6', 'data[JSON]' => $admin_data]);
                                        sm_admin(['product_add_5', $btn['id'], $name_product, $price, $min, $max], ['skip_back_panel', 0]);
                                    } else {
                                        sm_admin(['product_add_error_1']);
                                    }
                                } else {
                                    sm_admin(['product_add_error_2']);
                                }
                            } else {
                                sm_admin(['product_add_error_3']);
                            }
                        } else {
                            sm_admin(['product_add_error_4']);
                        }
                    } else {
                        sm_admin(['product_add_error_5']);
                    }
                } else {
                    $result_api = $db->get('apis', '*', ['id' => $admin_data['api']]);
                    if ($result_api) {
                        if ($result_api['smart_panel']) {
                            /** Smart Panel */
                            if (count($explode) >= 2 and count($explode) <= 5) {
                                $id = trim($explode['0']);
                                $price = trim($explode['1']);

                                $result_service = $api->services($result_api);
                                if ($result_service['result']) {
                                    foreach ($result_service['data'] as $service) {
                                        if ($service['service'] == $id) {
                                            $info_product = $service;
                                            break;
                                        }
                                    }
                                    if (isset($explode['2'])) {
                                        $name_product = $explode['2'];
                                    } else {
                                        $name_product = $info_product['name'];
                                    }
                                    if (strlen($name_product) <= 130) {

                                        if (isset($explode['3']) && isset($explode['4'])) {
                                            $min = $explode['3'];
                                            $max = $explode['4'];
                                        } else {
                                            $min = $info_product['min'];
                                            $max = $info_product['max'];
                                        }
                                        if ($min && $max) {
                                            if (is_numeric($price) and $min > 0 and $max > 0) {
                                                $btn = $db->get('categories', '*', ['id' => $admin_data['category_id']]);
                                                $name_en = js($name_product);
                                                if (!$db->has('products', ['name' => $name_en, 'category_id' => $btn['id']])) {
                                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $btn['id']]);
                                                    $ordering += 1;
                                                    $db->insert('products', [
                                                        'name' => $name_en,
                                                        'price' => $price,
                                                        'min' => $min,
                                                        'max' => $max,
                                                        'info' => null,
                                                        'api' => $result_api['name'],
                                                        'service' => $id,
                                                        'category_id' => $btn['id'],
                                                        'ordering' => $ordering,
                                                    ]);
                                                    $insert = $db->id();
                                                    if ($insert) {
                                                        $admin_data['id'] = $insert;
                                                        admin_data(['step' => 'add_product_6', 'data[JSON]' => $admin_data]);
                                                        sm_admin(['product_add_5', $btn['id'], $name_product, $price, $min, $max], ['skip_back_panel', 1]);
                                                    } else {
                                                        sm_admin(['product_add_error_1']);
                                                    }
                                                } else {
                                                    sm_admin(['product_add_error_2']);
                                                }
                                            } else {
                                                sm_admin(['product_add_error_3']);
                                            }
                                        } else {
                                            sm_admin(['product_add_error_6']);
                                        }
                                    } else {
                                        sm_admin(['product_add_error_4']);
                                    }
                                } else {
                                    sm_admin(['product_add_error_7']);
                                }
                            } else {
                                sm_admin(['product_add_error_8']);
                            }
                        } else {
                            /** Not Smart Panel */
                            if (count($explode) == 5) {
                                $name_product = removeWhiteSpace($explode[0]);
                                $name_en = js($name_product);
                                $price = trim($explode[2]);
                                $id = trim($explode[1]);
                                $min = trim($explode[3]);
                                $max = trim($explode[4]);
                                if (strlen($name_product) <= 130) {
                                    $btn = $db->get('categories', '*', ['id' => $admin_data['category_id']]);
                                    if (!$db->has('products', ['name' => $name_en, 'category_id' => $btn['id']])) {

                                        if (is_numeric($price) and is_numeric($min) and is_numeric($max) and $min > 0 and $max > 0) {

                                            $ordering = (int) $db->max('products', 'ordering', ['category_id' => $btn['id']]);
                                            $ordering += 1;

                                            $db->insert('products', [
                                                'name' => $name_en,
                                                'price' => $price,
                                                'min' => $min,
                                                'max' => $max,
                                                'info' => null,
                                                'api' => $result_api['name'],
                                                'service' => $id,
                                                'category_id' => $btn['id'],
                                                'ordering' => $ordering,
                                            ]);

                                            $insert = $db->id();
                                            if ($insert) {
                                                $admin_data['id'] = $insert;
                                                admin_data(['step' => 'add_product_6', 'data[JSON]' => $admin_data]);
                                                sm_admin(['product_add_5', $btn['id'], $name_product, $price, $min, $max], ['skip_back_panel', 0]);
                                            } else {
                                                sm_admin(['product_add_error_1']);
                                            }
                                        } else {
                                            sm_admin(['product_add_error_3']);
                                        }
                                    } else {
                                        sm_admin(['product_add_error_2']);
                                    }
                                } else {
                                    sm_admin(['product_add_error_4']);
                                }
                            }
                        }
                    } else {
                        sm_admin(['product_add_error_9']);
                    }
                }
            }
            break;
        case 'add_product_6':
            $admin_data = json_decode($admin['data'], true);

            if ($text == $key_admin['skip_add_info']) {

                $result = $db->select('apis', 'name', ['LIMIT' => 95]);

                admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);
                sm_admin(['product_add_repeat'], ['product_add_api', $result]);
            } elseif ($text == $key_admin['api_add_info']) {

                $result_api = $db->get('apis', '*', ['id' => $admin_data['api']]);
                if ($result_api) {
                    if ($result_api['smart_panel']) {
                        $result_service = $api->services($result_api);
                        if ($result_service['result']) {
                            $id = $db->get('products', 'service', ['id' => $admin_data['id']]);
                            foreach ($result_service['data'] as $service) {
                                if ($service['service'] == $id) {
                                    $info_product = $service;
                                    break;
                                }
                            }
                            $desc = removeWhiteSpace($info_product['desc']);
                            $db->update('products', ['info' => $desc], ['id' => $admin_data['id']]);

                            $result = $db->select('apis', 'name', ['LIMIT' => 95]);
                            admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);

                            sm_admin(['product_add_repeat_2', $desc], ['product_add_api', $result]);
                        } else {
                            sm_admin(['product_add_error_7']);
                        }
                    } else {
                        sm_admin(['product_add_error_10']);
                    }
                } else {
                    sm_admin(['product_add_error_9']);
                }
            } else {

                $text = removeWhiteSpace($text);
                $db->update('products', ['info' => $text], ['id' => $admin_data['id']]);
                admin_data(['step' => 'add_product_4', 'data[JSON]' => $admin_data]);

                sm_admin(['product_add_repeat_2', $desc], ['product_add_api', $result]);
            }
            break;
        case 'edit_1':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                admin_step('products');
                sm_admin(['products_1'], ['products_panel']);
            } else {
                if ($text == $key_admin['next_page'] or $text == $key_admin['prev_page']) {

                    $displaySettings = json_decode($settings['display_category'], true);
                    $now = $admin_data['offset_main'];

                    if ($text == $key_admin['next_page']) {
                        $str = $now + $displaySettings['page'];
                    } else {
                        $str = $now - $displaySettings['page'];
                    }

                    $result = get_category(['offset' => $str, 'status' => 1], null);
                    if ($result) {
                        $c = $db->count('categories', 'id', ['category_id' => null]);
                        $admin_data['offset_main'] = $str;
                        admin_data(['step' => 'edit_1', 'data[JSON]' => $admin_data]);

                        sm_admin(['edit_shop_1'], ['category_select_panel', $result, $c, null, $str]);
                    } else {
                        sm_admin(['edit_shop_error_2']);
                    }
                } else {
                    $result_categorys = $db->get('categories', '*', ['name' => js($text)]);
                    if ($result_categorys) {
                        sm_admin(['edit_category', $text], ['edit_category', 0, $result_categorys['id']]);

                        if ($db->has('categories', ['category_id' => $result_categorys['id']])) {
                            // under menu first
                            $result = get_category(['offset' => 0, 'status' => 1], $result_categorys['id']);
                            if ($result) {
                                $admin_data['offset_under'] = 0;
                                $admin_data['main_id'] = $result_categorys['id'];
                                $c = $db->count('categories', ['category_id' => $result_categorys['id']]);
                                admin_data(['step' => 'edit_2', 'data[JSON]' => $admin_data]);
                                sm_admin(['edit_shop_2'], ['category_select_panel', $result, $c, $result_categorys['id'], 0]);
                            } else {
                                // not category
                                sm_admin(['edit_shop_error_1']);
                            }
                        } else {
                            // product menu first type main
                            $result = get_products(['offset' => 0, 'status' => 1], $result_categorys['id']);
                            if ($result) {
                                $admin_data['offset_product'] = 0;
                                $admin_data['category'] = $result_categorys['id'];
                                $c = $db->count('products', ['category_id' => $result_categorys['id']]);

                                admin_data(['step' => 'edit_3', 'data[JSON]' => $admin_data]);

                                sm_admin(['edit_shop_3'], ['product_select_panel', $result, $c, $result_categorys['id'], 0]);
                            } else {
                                // not category
                                sm_admin(['edit_shop_error_0']);
                            }
                        }
                    } else {
                        sm_admin(['product_add_bot_key']);
                    }
                }
            }
            break;
        case 'edit_2':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $result = get_category(['offset' => $admin_data['offset_main'], 'status' => 1], null);
                if ($result) {
                    $c = $db->count('categories', 'id', ['category_id' => null]);
                    admin_data(['step' => 'edit_1']);

                    sm_admin(['edit_shop_1'], ['category_select_panel', $result, $c, null, $admin_data['offset_main']]);
                } else {
                    sm_admin(['edit_shop_error_2']);
                }
            } else {
                if ($text == $key_admin['next_page'] or $text == $key_admin['prev_page']) {

                    $displaySettings = json_decode($settings['display_sub_category'], true);
                    $now = $admin_data['offset_under'];

                    if ($text == $key_admin['next_page']) {
                        $str = $now + $displaySettings['page'];
                    } else {
                        $str = $now - $displaySettings['page'];
                    }

                    $result = get_category(['offset' => $str, 'status' => 1], $admin_data['main_id']);
                    if ($result) {
                        $c = $db->count('categories', 'id', ['category_id' => $admin_data['main_id']]);
                        $admin_data['offset_under'] = $str;
                        admin_data(['step' => 'edit_2', 'data[JSON]' => $admin_data]);

                        sm_admin(['edit_shop_2'], ['category_select_panel', $result, $c, $admin_data['main_id'], $str]);
                    } else {
                        sm_admin(['edit_shop_error_2']);
                    }
                } else {
                    $result_categorys = $db->get('categories', '*', ['name' => js($text), "category_id" => $admin_data['main_id']]);
                    if ($result_categorys) {
                        sm_admin(['edit_category', $text], ['edit_category', 0, $result_categorys['id']]);

                        $result = get_products(['offset' => 0, 'status'], $result_categorys['id']);
                        if ($result) {
                            $admin_data['offset_product'] = 0;
                            $admin_data['category'] = $result_categorys['id'];
                            $c = $db->count('products', ['category_id' => $result_categorys['id']]);
                            admin_data(['step' => 'edit_3', 'data[JSON]' => $admin_data]);

                            sm_admin(['edit_shop_3'], ['product_select_panel', $result, $c, $result_categorys['id'], 0]);
                        } else {
                            // not category
                            sm_admin(['edit_shop_error_3']);
                        }
                    } else {
                        sm_admin(['product_add_bot_key']);
                    }
                }
            }
            break;
        case 'edit_3':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $result_categorys = $db->get('categories', '*', ['id' => $admin_data['category']]);
                if ($result_categorys) {
                    if ($result_categorys['category_id'] == null) {

                        $result = get_category(['offset' => $admin_data['offset_main'], 'status' => 1], null);
                        if ($result) {
                            $c = $db->count('categories', 'id', ['category_id' => null]);
                            admin_data(['step' => 'edit_1']);

                            sm_admin(['edit_shop_1'], ['category_select_panel', $result, $c, null, $admin_data['offset_main']]);
                        } else {
                            sm_admin(['edit_shop_error_2']);
                        }
                    } else {
                        $result = get_category(['offset' => $admin_data['offset_under'], 'status' => 1], $result_categorys['category_id']);
                        if ($result) {
                            $c = $db->count('categories', 'id', ['category_id' => $result_categorys['category_id']]);
                            admin_data(['step' => 'edit_2']);

                            sm_admin(['edit_shop_2'], ['category_select_panel', $result, $c, $result_categorys['category_id'], $admin_data['offset_under']]);
                        } else {
                            sm_admin(['edit_shop_error_2']);
                        }
                    }
                } else {
                    sm_admin(['product_add_bot_key']);
                }
            } else {
                if ($text == $key_admin['next_page'] or $text == $key_admin['prev_page']) {

                    $displaySettings = json_decode($settings['display_sub_category'], true);
                    $now = $admin_data['offset_product'];

                    if ($text == $key_admin['next_page']) {
                        $str = $now + $displaySettings['page'];
                    } else {
                        $str = $now - $displaySettings['page'];
                    }

                    $result = get_products(['offset' => $str, 'status' => 1], $admin_data['category']);
                    if ($result) {
                        $admin_data['offset_product'] = $str;
                        $c = $db->count('products', ['category_id' => $admin_data['category']]);
                        admin_data(['step' => 'edit_3', 'data[JSON]' => $admin_data]);

                        sm_admin(['edit_shop_3'], ['product_select_panel', $result, $c, $admin_data['category'], $str]);
                    } else {
                        // not category
                        sm_admin(['edit_shop_error_3']);
                    }
                } else {

                    $result_product = $db->get('products', '*', ['category_id' => $admin_data['category'], 'name' => js($text)]);

                    if ($result_product) {
                        sm_admin(['edit_product', $text], ['edit_category', 1, $result_product['id']]);
                    } else {
                        sm_admin(['product_add_bot_key']);
                    }
                }
            }
            break;
        case 'edit_info':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $result = get_category(['offset' => 0, 'status' => 1], null);
                if ($result) {
                    $c = $db->count('categories', ['category_id' => null]);
                    $admin_data = ['offset_main' => 0];
                    admin_data(['step' => 'edit_1', 'data[JSON]' => $admin_data]);
                    sm_admin(['edit_shop_1'], ['category_select_panel', $result, $c, null, 0]);
                } else {
                    // not category
                    sm_admin(['edit_shop_error_1']);
                }
            } else {
                $type = $admin_data['type'];

                switch ($text) {
                    case $key_admin['product_edit_option']['name']:
                        $admin_data['type_edit'] = 'name';
                        admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_info', 'name', 0], ['back_panel']);
                        break;

                    case $key_admin['product_edit_option']['ordering']:
                        if ($type == 'product') {
                            $r  = $db->get('products', '*', ['id' => $admin_data['id']]);
                            $m  = (int) $db->min('products', 'ordering', ['category_id' => $r['category_id']]);
                            $m2 = (int) $db->max('products', 'ordering', ['category_id' => $r['category_id']]);
                        } else {
                            $r = $db->get('categories', '*', ['id' => $admin_data['id']]);
                            if ($r['category_id'] == null) {
                                $m = (int) $db->min('categories', 'ordering', ['category_id' => null]);
                                $m2 = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                            } else {
                                $m  = (int) $db->min('categories', 'ordering', ['category_id' => $r['category_id']]);
                                $m2 = (int) $db->max('categories', 'ordering', ['category_id' => $r['category_id']]);
                            }
                        }

                        $admin_data['type_edit'] = 'ordering';
                        admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);

                        sm_admin(['edit_info', 'ordering', $m, $m2], ['back_panel']);
                        break;

                    case $key_admin['product_edit_option']['delete']:
                        $admin_data['type_edit'] = 'delete';
                        admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                        sm_admin(['edit_info', 'delete', 0], ['ok_admin_panel']);
                        break;

                    default:
                        if ($type == 'product') {
                            switch ($text) {
                                case $key_admin['product_edit_option']['price']:
                                    $admin_data['type_edit'] = 'price';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'price', 0], ['back_panel']);
                                    break;
                                case $key_admin['product_edit_option']['min']:
                                    $admin_data['type_edit'] = 'min';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'min', 0], ['back_panel']);
                                    break;
                                case $key_admin['product_edit_option']['info']:
                                    $admin_data['type_edit'] = 'info';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'info', 0], ['back_panel']);
                                    break;
                                case $key_admin['product_edit_option']['api']:
                                    $admin_data['type_edit'] = 'api';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);

                                    $result = $db->select('apis', 'name', ['LIMIT' => 95]);
                                    sm_admin(['edit_info', 'api', 0], ['api_select_panel', $result, true]);
                                    break;
                                case $key_admin['product_edit_option']['discount']:
                                    $admin_data['type_edit'] = 'discount';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'discount', 0], ['back_panel']);
                                    break;
                                case $key_admin['product_edit_option']['confirm']:
                                    $admin_data['type_edit'] = 'confirm';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'confirm', 0], ['back_panel']);
                                    break;
                                case $key_admin['product_edit_option']['service']:
                                    $admin_data['type_edit'] = 'service';
                                    admin_data(['step' => 'edit_info_2', 'data[JSON]' => $admin_data]);
                                    sm_admin(['edit_info', 'service', 0], ['back_panel']);
                                    break;
                            }
                        }
                        break;
                }
            }
            break;
        case 'edit_info_2':
            $admin_data = json_decode($admin['data'], true);
            if ($text == $key_admin['back_admin_before']) {
                $type = $admin_data['type'];
                $id = $admin_data['id'];
                switch ($type) {
                    case 'category':
                        $result = $db->get('categories', '*', ['id' => $id]);
                        if ($result['category_id'] == null) {
                            admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'category', 'id' => $id]]);

                            $un = $db->has('categories', ['category_id' => $result['id']]);

                            sm_admin(['edit_category_info', $result, $un, false], ['update_info', 'category', $result['id']]);

                            sm_admin(['edit_products_panel'], ['edit_products_panel', 'category']);
                        } else {
                            admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'under', 'id' => $id]]);

                            sm_admin(['edit_under_info', $result, false], ['update_info', 'under', $result['id']]);

                            sm_admin(['edit_products_panel'], ['edit_products_panel', 'under']);
                        }
                        break;
                    case 'product':
                        $result = $db->get('products', '*', ['id' => $id]);

                        admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'product', 'id' => $id]]);

                        sm_admin(['edit_product_info', $result, false], ['update_info', 'product', $result['id']]);

                        sm_admin(['edit_products_panel'], ['edit_products_panel', 'product']);

                        break;
                }
            } else {
                $type_edit = $admin_data['type_edit'];
                $type = $admin_data['type'];
                $id = $admin_data['id'];
                $true = false;
                switch ($type_edit) {
                    case 'name':
                        $name_product = removeWhiteSpace($text);
                        $name_en = js($name_product);
                        if ($type == 'product') {
                            if (strlen($name_product) <= 130) {
                                $product = $db->get('products', '*', ['id' => $id]);
                                if (!$db->has('products', ['name' => $name_en, 'category_id' => $product['category_id']])) {

                                    $db->update('products', ['name' => $name_en], ['id' => $id]);
                                    $true = true;
                                } else {
                                    sm_admin(['error_edit_product_1']);
                                }
                            } else {
                                sm_admin(['error_edit_product_8']);
                            }
                        } else {
                            if (strlen($name_product) <= 130) {
                                $category = $db->get('categories', '*', ['id' => $id]);
                                if (!$db->has('categories', ['name' => $name_en, 'category_id' => $category['category_id']])) {

                                    $db->update('categories', ['name' => $name_en], ['id' => $id]);
                                    $true = true;
                                } else {
                                    sm_admin(['error_edit_product_1']);
                                }
                            } else {
                                sm_admin(['error_edit_product_8']);
                            }
                        }

                        break;
                    case 'ordering':
                        if (is_numeric($text)) {
                            if ($type == 'product') {
                                $db->update('products', ['ordering' => $text], ['id' => $id]);
                            } else {
                                $db->update('categories', ['ordering' => $text], ['id' => $id]);
                            }
                            $true = true;
                        } else {
                            sm_admin(['error_edit_product_2']);
                        }
                        break;
                    case 'delete':
                        if ($text == $key_admin['ok_admin']) {
                            if ($type == 'product') {
                                $db->delete('products', ['id' => $id]);
                                admin_step('products');
                                sm_admin(['ok_delete_products'], ['products_panel']);
                            } else {
                                if ($db->has('categories', ['category_id' => $id])) {

                                    $r2 = $db->select('categories', '*', ['category_id' => $id]);
                                    foreach ($r2 as $row) {
                                        $db->delete('products', ['category_id' => $row['id']]);
                                        $db->delete('categories', ['id' => $row['id']]);
                                    }
                                    $db->delete('categories', ['id' => $id]);
                                } else {
                                    $db->delete('products', ['category_id' => $id]);
                                    $db->delete('categories', ['id' => $id]);
                                }
                                admin_step('products');
                                sm_admin(['ok_delete_products'], ['products_panel']);
                            }
                        }
                        break;
                    case 'price':
                        if (is_numeric($text) && $text > 0) {
                            $db->update('products', ['price' => $text], ['id' => $id]);
                            $true = true;
                        } else {
                            sm_admin(['error_edit_product_3']);
                        }
                        break;
                    case 'min':
                        $ex = explode("\n", $text);
                        if (count($ex) == 2 and is_numeric($ex[0]) and is_numeric($ex[1])) {
                            $min = $ex[0];
                            $max = $ex[1];
                            if ($min > 0 and $max > 0) {
                                $db->update('products', ['min' => $min, 'max' => $max], ['id' => $id]);
                                $true = true;
                            }
                        } else {
                            sm_admin(['error_edit_product_4']);
                        }
                        break;
                    case 'info':
                        $text = removeWhiteSpace($text);
                        $db->update('products', ['info' => $text], ['id' => $id]);
                        $true = true;
                        break;
                    case 'api':
                        if ($text == $key_admin['no_api']) {
                            $db->update('products', ['api' => 'noapi', 'service' => 0], ['id' => $id]);
                            $true = true;
                        } else {
                            if ($db->has('apis', ['name' => js($text)])) {
                                $db->update('products', ['api' => js($text)], ['id' => $id]);
                                $true = true;
                            } else {
                                sm_admin(['error_edit_product_5']);
                            }
                        }
                        break;
                    case 'discount':
                        if (is_numeric($text) and $text >= 0 and $text <= 100) {
                            $db->update('products', ['discount' => $text], ['id' => $id]);
                            $true = true;
                        } else {
                            sm_admin(['error_edit_product_6']);
                        }
                        break;
                    case 'confirm':
                        if (is_numeric($text) and $text >= 0) {
                            $db->update('products', ['confirm' => $text], ['id' => $id]);
                            $true = true;
                        } else {
                            sm_admin(['error_edit_product_7']);
                        }
                        break;
                    case 'service':
                        $db->update('products', ['service' => $text], ['id' => $id]);
                        $true = true;

                        break;
                }

                if ($true) {
                    switch ($type) {
                        case 'category':
                        case 'under':
                            $result = $db->get('categories', '*', ['id' => $id]);
                            if ($result['category_id'] == null) {
                                admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'category', 'id' => $id]]);

                                $un = $db->has('categories', ['category_id' => $result['id']]);

                                sm_admin(['edit_category_info', $result, $un, false], ['update_info', 'category', $result['id']]);

                                sm_admin(['edit_products_panel'], ['edit_products_panel', 'category']);
                            } else {
                                admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'under', 'id' => $id]]);

                                sm_admin(['edit_under_info', $result, false], ['update_info', 'under', $result['id']]);

                                sm_admin(['edit_products_panel'], ['edit_products_panel', 'under']);
                            }
                            break;
                        case 'product':
                            $result = $db->get('products', '*', ['id' => $id]);

                            admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'product', 'id' => $id]]);

                            sm_admin(['edit_product_info', $result, false], ['update_info', 'product', $result['id']]);

                            sm_admin(['edit_products_panel'], ['edit_products_panel', 'product']);

                            break;
                    }
                }
            }
            break;
        case 'update_api_1':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('products');
                sm_admin(['products_1'], ['products_panel']);
            } else {
                $get = $db->get('apis', '*', ['name' => js($text)]);
                if ($get) {
                    admin_data(['step' => 'update_api_2', 'data[JSON]' => ['api_id' => $get['id']]]);
                    $r = sm_admin(['.'], ['back_panel'])['result']['message_id'];
                    $bot->delete_msg($fid, $r);
                    sm_admin(['update_api_2'], ['update_api_type_0']);
                } else {
                }
            }
            break;
        case 'update_api_2':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('products');
                sm_admin(['products_1'], ['products_panel']);
            }
            break;
        case 'update_api_41':
            $admin_data = json_decode($admin['data'], true);
            $r = $admin_data['msgid2'];
            switch ($admin_data['type']) {
                case 'up':
                    if (is_numeric($text) && $text >= 0) {
                        $bot->delete_msg($fid, $message_id);
                        $bot->delete_msg($fid, $r);

                        $p_s = $admin_data['p_s'];
                        $p_s['up'] = $text;

                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);

                        edk_admin(['update_api_product_settings', $admin_data['update_type'], $p_s], $admin_data['msgid1']);
                    }
                    break;
                case 'round':
                    if (is_numeric($text)) {
                        $bot->delete_msg($fid, $message_id);
                        $bot->delete_msg($fid, $r);
                        $p_s = $admin_data['p_s'];
                        $p_s['round'] = $text;

                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);

                        edk_admin(['update_api_product_settings', $admin_data['update_type'], $p_s], $admin_data['msgid1']);
                    }
                    break;
            }
            break;
        case 'delete_all':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('products');
                sm_admin(['products_1'], ['products_panel']);
            } else {
                if (in_array($text, array_values($key_admin['product_delete_option']))) {
                    $str = array_search($text, $key_admin['product_delete_option']);
                    $admin_data['type'] = $str;
                    admin_data(['step' => 'delete_all_1', 'data[JSON]' => $admin_data]);
                    sm_admin(['delete_all_2', $text], ['ok_admin_panel']);
                }
            }
            break;
        case 'delete_all_1':
            if ($text == $key_admin['back_admin_before']) {
                admin_step('delete_all');
                sm_admin(['type_of_delete'], ['type_of_delete']);
            } else {
                $admin_data = json_decode($admin['data'], true);
                $type = $admin_data['type'];
                switch ($type) {
                    case 'all':
                        $db->delete('categories', []);
                        $db->delete('products', []);
                        break;
                    case 'products':
                        $db->delete('products', []);
                        break;
                    case 'products_off':
                        $db->delete('products', ['status' => 0]);
                        break;
                    case 'category_off':
                        $result = $db->select('categories', '*', ['status' => 0, 'category_id' => null]);
                        foreach ($result as $row) {
                            if (!$db->has('categories', ['category_id' => $row['id']])) {
                                $db->delete('categories', ['id' => $row['id']]);
                                $db->delete('products', ['category_id' => $row['id']]);
                            } else {
                                $r2 = $db->select('categories', '*', ['category_id' => $row['id']]);
                                foreach ($r2 as $row2) {
                                    $db->delete('products', ['category_id' => $row2['id']]);
                                    $db->delete('categories', ['id' => $row2['id']]);
                                }
                                $db->delete('categories', ['id' => $row['id']]);
                            }
                        }
                        break;
                    case 'under_off':
                        $result = $db->select('categories', '*', ['status' => 0, 'category_id[!]' => null]);
                        foreach ($result as $row) {
                            $db->delete('products', ['category_id' => $row['id']]);
                            $db->delete('categories', ['id' => $row['id']]);
                        }
                        break;
                    case 'category_empty':
                        $result = $db->select('categories', '*', ['category_id' => null]);
                        foreach ($result as $row) {
                            if (!$db->has('categories', ['category_id' => $row['id']])) {
                                if (!$db->has('products', ['category_id' => $row['id']])) {
                                    $db->delete('categories', ['id' => $row['id']]);
                                }
                            } else {
                                $r2 = $db->select('categories', '*', ['category_id' => $row['id']]);
                                foreach ($r2 as $row2) {
                                    if (!$db->has('products', ['category_id' => $row2['id']])) {
                                        $db->delete('categories', ['id' => $row2['id']]);
                                    }
                                }
                                if (!$db->has('categories', ['category_id' => $row['id']])) {
                                    $db->delete('categories', ['id' => $row['id']]);
                                }
                            }
                        }
                        break;
                }
                admin_step('products');
                sm_admin(['delete_all_3'], ['products_panel']);
            }
            break;
        case 'send_answer_1':
            $decode = json_decode($admin['data'], true);
            $id = $decode['id'];
            $msgid = $decode['msgid'];
            $chat = $decode['chat'];
            $delid = $decode['remsg'];

            if (isset($text)) {
                sm_to_user(['support_pm2', $text], ['fast_support'], $id);

                if ($fid != $chat['id']) {
                    $bot->bot('sendmessage', [
                        'chat_id' => $chat,
                        'text' => $media_admin->atext('send_pm_result', $text),
                        'reply_to_message_id' => $msgid,
                        'parse_mode' => 'Html',
                        'disable_web_page_preview' => true,
                    ]);
                }
            } else {
                $types = ['video', 'photo', 'audio', 'voice', 'document'];
                foreach ($types as $i) {
                    if (isset($update['message'][$i])) {
                        $type = $update['message'][$i];
                        @$caption = $update['message']['caption'];
                        if ($i == 'photo') {
                            $file_id = $type[count($type) - 1]['file_id'];
                        } else {
                            $file_id = $type['file_id'];
                        }
                        break;
                    }
                }
                $send = str_replace($types, ['sendvideo', 'sendphoto', 'sendaudio', 'sendvoice', 'senddocument'], $i);
                $r = $bot->bot($send, [
                    'chat_id' => $id,
                    $i => $file_id,
                    'caption' => $media->atext('support_pm2', $caption),
                    'parse_mode' => 'Html',
                    'reply_markup' => json_encode($media->akeys('fast_support')),
                ]);
                if ($fid != $chat['id']) {
                    $bot->bot($send, [
                        'chat_id' => $chat,
                        $i => $file_id,
                        'caption' => $media_admin->atext('send_pm_result', $caption),
                        'parse_mode' => 'Html',
                        'reply_to_message_id' => $msgid,
                        'reply_markup' => $admin_home
                    ]);
                }
            }
            $bot->delete_msg($cid, $delid);
            admin_data(['step' => 'none', 'data' => 'none']);
            sm_admin(['send_answer_ok'], ['home', $access]);
            break;
        case 'set_user_card':
            $decode = json_decode($admin['data'], true);
            $id = $decode['id'];
            $msgid = $decode['msgid'];
            $chat = $decode['chat'];
            $delid = $decode['remsg'];

            if (is_numeric($text)) {
                user_set_data(['payment_card' => $text], $id);
                sm_to_user(['ok_card'], null, $id);
                $bot->delete_msg($cid, $delid);
                admin_data(['step' => 'none', 'data' => 'none']);
                sm_admin(['set_user_card_ok'], ['home', $access]);
            } else {
                sm_admin(['set_user_card_int']);
            }

            break;
        case 'value':
            # code...
            break;
        case 'value':
            # code...
            break;
        case 'value':
            # code...
            break;
        case 'value':
            # code...
            break;
        case 'value':
            # code...
            break;
        case 'value':
            # code...
            break;
        default:
            sm_admin(['error']);
            break;
    }
}
