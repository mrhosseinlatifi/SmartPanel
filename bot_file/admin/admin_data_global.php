<?php

function admin_data_global()
{
    extract($GLOBALS);

    switch ($data) {
        case  'backadmin':
        case  'admin_back':
            $bot->delete_msg($fid, $message_id);
            admin_data(['step' => 'none', 'data' => 'none']);
            user_set_step('admin');
            sm_admin(['start_panel'], ['home', $access]);
            break;
        case 'close_panel':
            $bot->delete_msg($fid, $message_id);
            break;
        case 'fyk':
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminorderch_'):
            $str = str_replace('adminorderch_', '', $data);
            $ex = explode('-', $str);
            $type = $ex['0'];
            $code = $ex['1'];
            $show_channel = get_option('channel_main', 0);
            $order = $db->get('orders', '*', ['id' => $code]);
            if (!$order) {
                alert_admin(['not_found_order', $code]);
                break;
            }
            $user_id = $order['user_id'];
            $usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
            $old_balance = $usResult['balance'];

            switch ($type) {
                case 'go':
                    check_allow('ch_order', 'sub');
                    if ($order['api'] == 'noapi') {
                        if ($order['status'] == 'pending') {
                            $db->update('orders', ['status' => 'in progress'], ['id' => $order['id']]);
                            sm_user(['order_go', $order, $show_channel], null, $user_id);
                            edk_channel('channel_order_noapi', ['order_noapi', $code, 'inprogress']);
                        } elseif ($order['status'] == 'in progress') {
                            edk_channel('channel_order_noapi', ['order_noapi', $code, 'inprogress']);
                        }
                    }
                    break;
                case 'check':
                    alert_admin(['status_order', $order], true);
                    break;
                case 'confirm':
                    check_allow('ch_order', 'sub');
                    if ($order['api'] != 'noapi') {
                        $db->update('orders', ['status' => 'pending'], ['id' => $order['id']]);
                        alert_admin(['confirm_order', $order]);
                        edk_channel('channel_order_noapi', ['order_api', $code, 'confirm']);
                    }
                    break;
                case 'cancel':
                    check_allow('ch_order', 'sub');
                    if ($order['api'] == 'noapi') {
                        if ($order['status'] == 'pending' || $order['status'] == 'in progress') {
                            $db->update('orders', ['status' => 'canceled'], ['id' => $order['id']]);
                            $db->update('users_information', ['balance[+]' => $order['price'], 'amount_spent[-]' => $order['price']], ['user_id' => $user_id]);
                            sm_user(['order_cancel', $order, $show_channel], null, $user_id);
                            alert_admin(['cancel_order', $order]);
                            edk_channel('channel_order_noapi', ['order_noapi', $code, 'cancel']);

                            $new_balance = $old_balance + $order['price'];
                            insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $order['price'], $order['id']);
                        }
                    } else {
                        if ($order['status'] == 'pending' && $order['code_api'] == 0) {
                            $db->update('orders', ['status' => 'canceled'], ['id' => $order['id']]);
                            $db->update('users_information', ['balance[+]' => $order['price'], 'amount_spent[-]' => $order['price']], ['user_id' => $user_id]);
                            sm_user(['order_cancel', $order, $show_channel], null, $user_id);
                            alert_admin(['cancel_order', $order]);
                            edk_channel('channel_order_noapi', ['order_noapi', $code, 'cancel']);
                            $new_balance = $old_balance + $order['price'];
                            insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $order['price'], $order['id']);
                        } else {
                            alert_admin(['cant_cancel_order', $order]);
                        }
                    }
                    break;
                case 'completed':
                    check_allow('ch_order', 'sub');
                    if ($order['status'] == 'in progress') {
                        $db->update('orders', ['status' => 'completed'], ['id' => $order['id']]);
                        sm_user(['order_confirmation', $order, $show_channel], null, $user_id);
                        edk_channel('channel_order_noapi', ['order_noapi', $code, 'completed']);
                    }
                    break;
                default:
                    # code...
                    break;
            }
            // update_api_ok_1
            break;
        case text_starts_with($data, 'admintiket_'):
            check_allow('support', 'sub');
            $str = str_replace('admintiket_', '', $data);
            $ex = explode('_', $str);
            $type = $ex['0'];
            $user_id = $ex['1'];
            if ($tc == 'channel') {
                if (isset($message['chat']['username'])) {
                    $uid = $message['chat']['username'];
                } else {
                    $uid = $cid;
                }
                $link = 'https://t.me/' . $uid . '/' . $message_id;
            }
            switch ($type) {
                case 'answer':
                    user_set_step('admin');
                    $mm = sm_admin(['send_answer', $link], ['back_panel_all'])['result']['message_id'];
                    admin_data(['step' => 'send_answer_1', 'data[JSON]' => ['id' => $user_id, 'chat' => $cid, 'msgid' => $message_id, 'remsg' => $mm]]);
                    user_set_data(['ticket[-]' => 1], $user_id);
                    edk_admin(['support', 1, $user_id], $message_id, $cid);
                    break;
                case 'again':
                    user_set_step('admin');
                    $mm = sm_admin(['send_answer', $link], ['back_panel_all'])['result']['message_id'];
                    admin_data(['step' => 'send_answer_1', 'data[JSON]' => ['id' => $user_id, 'chat' => $cid, 'msgid' => $message_id, 'remsg' => $mm]]);
                    user_set_data(['ticket[-]' => 1], $user_id);
                    break;
                case 'rad':
                    user_set_data(['ticket[-]' => 1], $user_id);
                    edk_admin(['support', 2, $user_id], $message_id, $cid);
                    break;
                case 'info':
                    $result = $db->get('users_information', '*', ['user_id' => $user_id]);
                    $id = $result['user_id'];
                    $name = $bot->getChatMember($id);
                    sm_admin(['userinfo_2', $result, $name], ['userinfo_data', $id]);
                    break;
                default:
                    # code...
                    break;
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminupuser_'):
            check_allow('userinfo');
            $id = str_replace('adminupuser_', '', $data);
            $name = $bot->getChatMember($id);
            $res = $db->get('users_information', '*', ['user_id' => $id]);
            edt_admin(['userinfo_2', $res, $name, 1], ['userinfo_data', $id]);
            break;
        case text_starts_with($data, 'adminorder_'):
            // User Orders Logs
            $str = str_replace('adminorder_', '', $data);
            $ex = explode('_', $str);
            $id = $ex[1];
            $page = 10;
            $now = $ex[0];
            $nex = $now + $page;
            $bef = $now - $page;
            $result = null;
            $c = 0;
            $result = $db->select('orders', '*', ['user_id' => $id, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [$now, $page]]);
            $c = $db->count('orders', ['user_id' => $id]) ?? 0;
            edt_admin(['userinfo_orders', $result, $id, $nex, $page, $c, $db, $api], ['userinfo_data_page', 'adminorder', $c, $id, $nex, $bef]);
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminpayment_'):
            // User Payment History
            $str = str_replace('adminpayment_', '', $data);
            $ex = explode('_', $str);
            $id = $ex[1];
            $page = 10;
            $now = $ex[0];
            $nex = $now + $page;
            $bef = $now - $page;
            $result = null;
            $c = 0;
            // All payment related transaction types
            $payment_types = ['payment', 'payment_offline'];
            $result = $db->select('transactions', '*', ['user_id' => $id, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [$now, $page], 'type' => $payment_types]);
            $c = $db->count('transactions', ['user_id' => $id, 'type' => $payment_types]) ?? 0;
            edt_admin(['userinfo_payments', $result, $id, $nex, $page, $c], ['userinfo_data_page', 'adminpayment', $c, $id, $nex, $bef]);
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'admintrans_'):
            // Users Log Balance
            $str = str_replace('admintrans_', '', $data);
            $ex = explode('_', $str);
            $id = $ex[1];
            $page = 10;
            $now = $ex[0];
            $nex = $now + $page;
            $bef = $now - $page;
            $result = null;
            $c = 0;
            $balance_log_types = [
                'gift',
                'gift_code',
                'gift_payout',
                'gift_move',
                'managment',
                'send_balance',
                'receive_balance',
                'orders',
                'orders_back'
            ];
            $result = $db->select('transactions', '*', ['user_id' => $id, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [$now, $page], 'type' => $balance_log_types]);
            $c = $db->count('transactions', ['user_id' => $id, 'type' => $balance_log_types]);
            edt_admin(['userinfo_trs', $result, $id, $nex, $page, $c], ['userinfo_data_page', 'admintrans', $c, $id, $nex, $bef]);
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'verifycard-'):
            check_allow('card', 'sub');
            $str = str_replace('verifycard-', '', $data);
            $ex = explode('-', $str);
            $type = $ex['0'];
            $user_id = $ex['1'];
            $result = $db->get('users_information', '*', ['user_id' => $user_id]);
            switch ($type) {
                case 'ok':
                    if ($tc == 'channel') {
                        if (isset($message['chat']['username'])) {
                            $uid = $message['chat']['username'];
                        } else {
                            $uid = $cid;
                        }
                        $link = 'https://t.me/' . $uid . '/' . $message_id;
                    }
                    user_set_step('admin');
                    $mm = sm_admin(['set_user_card', $link], ['back_panel_all'])['result']['message_id'];
                    admin_data(['step' => 'set_user_card', 'data[JSON]' => ['id' => $user_id, 'chat' => $cid, 'msgid' => $message_id, 'remsg' => $mm]]);
                    edk_admin(['verify_keys', 1], $message_id, $cid);
                    alert_admin(['none']);
                    break;
                case 'nok':
                    if ($result['payment_card'] == 'wait') {
                        user_set_data(['payment_card' => 0], $user_id);
                        sm_to_user(['reject_card'], null, $user_id);
                        edk_admin(['verify_keys', 2], $message_id, $cid);
                    }
                    alert_admin(['none']);
                    break;
                case 'info':

                    $id = $result['user_id'];
                    $name = $bot->getChatMember($id);
                    sm_admin(['userinfo_2', $result, $name], ['userinfo_data', $id]);
                    alert_admin(['sended']);
                    break;
                default:
                    # code...
                    break;
            }
            break;
        case text_starts_with($data, 'payout_'):
            check_allow('payout', 'sub');
            $str = str_replace('payout_', '', $data);
            $ex = explode('_', $str);
            $type = $ex['0'];
            $code = $ex['1'];
            $payout = $db->get('transactions', '*', ['id' => $code]);
            switch ($type) {
                case 'ok':
                    $db->update('transactions', ['status' => 1], ['id' => $code]);
                    edk_channel('channel_gift_transaction', ['gift_payouts', 1]);
                    sm_to_user(['ok_payout'], null, $payout['user_id']);
                    alert_admin(['none']);
                    break;
                case 'nok':
                    $db->update('transactions', ['status' => 0], ['id' => $code]);
                    $db->update('users_information', ["gift[+]" => $payout['amount']], ['user_id' => $payout['user_id']]);
                    edk_channel('channel_gift_transaction', ['gift_payouts', 2]);
                    sm_to_user(['cancel_payout'], null, $payout['user_id']);
                    alert_admin(['none']);
                    break;
                default:
                    # code...
                    break;
            }
            break;
        case 'statistics':
            $number_user = number_format($db->count('users_information')) ?: 0;
            $today1 = date('Y/m/d');
            $today = strtotime("now");
            $tom = strtotime($today1 . "+1 day");
            $yes = strtotime($today1 . "-1 day");
            $yes2 = strtotime($today1 . "-2 day");
            $today_member = number_format($db->count('users_information', 'user_id', ['join_date[<>]' => [$yes, $today]])) ?: 0;
            $yesterdate_member = number_format($db->count('users_information', 'user_id', ['join_date[<>]' => [$yes2, $yes]])) ?: 0;
            $number_user_block = number_format($db->count('users_information', ['block' => 1])) ?: 0;
            $number_order = number_format($db->count('orders')) ?: 0;
            $order_cheack_api = number_format($db->count('orders', ['api[!]' => 'noapi', 'status' => ['pending', 'in progress']])) ?: 0;
            $order_cheack_no_api = number_format($db->count('orders', ['api' => 'noapi', 'status' => 'pending'])) ?: 0;
            $number_payment = number_format($db->count('transactions', ['status' => ['OK', 100], 'type' => ['payment', 'payment_offline']])) ?: 0;
            $number_payment_creted = number_format($db->count('transactions', 'id', ['type' => ['payment', 'payment_offline']])) ?: 0;

            $ba1 = ($db->sum('users_information', 'balance') > 0) ? $db->sum('users_information', 'balance') : 0;
            $users_balance = number_format($ba1) ?: 0;

            $ba2 = ($db->sum('users_information', 'gift') > 0) ? $db->sum('users_information', 'gift') : 0;
            $users_gift_balance = number_format($ba2) ?: 0;

            $ba3 = ($db->sum('transactions', 'amount', ['status' => 1, 'type' => ['payment', 'payment_offline']]) > 0) ? $db->sum('transactions', 'amount', ['status' => 1, 'type' => ['payment', 'payment_offline']]) : 0;
            $amount_paid = number_format($ba3) ?: 0;

            $cron = $settings['last_cron_send'];
            $cron_done = $settings['last_cron_orders'];
            edt_admin([
                'more_stats',
                $number_user,
                $today_member,
                $yesterdate_member,
                $number_user_block,
                $number_order,
                $order_cheack_api,
                $order_cheack_no_api,
                $number_payment,
                $number_payment_creted,
                $users_balance,
                $users_gift_balance,
                $amount_paid,
                $cron,
                $cron_done,
                $settings['version']
            ], ['more_stats', 1]);
            break;
        case text_starts_with($data, 'adminopen_section_'):
            check_allow('status');
            $str = str_replace('adminopen_section_', '', $data);
            $result = $section_status[$str];
            edk_admin(['sub_off', $result, $str]);
            break;
        case text_starts_with($data, 'adminsection_status_'):
            check_allow('status');
            $str = str_replace('adminsection_status_', '', $data);
            $ex = explode('-', $str);
            if ($section_status[$ex[0]][$ex[1]] == 1) {
                $section_status[$ex[0]][$ex[1]] = 0;
                update_option('section_status', json_encode($section_status));
                alert_admin(['offline_status']);
            } else {
                $section_status[$ex[0]][$ex[1]] = 1;
                update_option('section_status', json_encode($section_status));
                alert_admin(['online_status']);
            }
            $res = $section_status[$ex[0]];
            if ($ex[0] == 'main') {
                edk_admin(['status', $section_status['main'], $section_status]);
            } else {
                edk_admin(['sub_off', $res, $ex[0]]);
            }
            break;
        case 'adminupsection_status':
            check_allow('status');
            edt_admin(['status'], ['status', $section_status['main'], $section_status]);
            alert_admin(['update_status']);
            break;
        case text_starts_with($data, 'verifyreceipt-'):
            check_allow('verifyreceipt', 'sub');
            $str = str_replace('verifyreceipt-', '', $data);
            list($type, $id) = explode('-', $str);
            $invoice = $db->get('transactions', '*', ['id' => $id]);
            if ($invoice['status'] == 2) {
                $amount = $invoice['amount'];
                $userId = $invoice['user_id'];
                $name = $bot->getChatMember($userId)['user']['first_name'];
                $user1 = $db->get('users_information', '*', ['user_id' => $userId]);
                $decode = json_decode($invoice['data'], true);
                $decode['admin'] = $fid;

                switch ($type) {
                    case 'ok':
                        // up balance
                        user_set_data(['balance[+]' => $amount, 'amount_paid[+]' => $amount], $userId);
                        // ref
                        if ($user1["referral_id"] > 0 && !text_contains($user1["referral_id"], 'off') && $section_status['main']['free'] && $section_status['free']['gift_payment'] && $fid != $user1["referral_id"]) {
                            $gifi = (($amount * $settings['gift_payment']) / 100);

                            $usResult = $db->get('users_information', '*', ['user_id' => $user1["referral_id"]]);
                            $old_balance = $usResult['balance'];
                            $new_balance = $old_balance + $gifi;
                            insertTransaction('gift', $user1["referral_id"], $old_balance, $new_balance, $gifi, 'GiftPayment');

                            $db->update('users_information', ['gift[+]' => $gifi, 'gift_payment[+]' => $gifi], ['user_id' => $user1["referral_id"]]);
                            $bot->sm($user1["referral_id"], $media->text('refral_gift_payment', [$userId, $name, $amount, $gifi]));
                        }

                        $new = $user1['balance'] + $amount;
                        $db->update('transactions', ['status' => 1, 's_date' => time(), 'data[JSON]' => $decode], ['id' => $invoice['id']]);

                        sm_to_user(['receipt_up', $amount, $new], null, $userId);

                        edk_admin(['receipt_check', 'OK'],$message_id,$cid);

                        break;
                    case 'nok':
                        $db->update('transactions', ['status' => 0, 's_date' => time(), 'data[JSON]' => $decode], ['id' => $invoice['id']]);

                        admin_data(['step' => 'send_reason_receipt', 'data[JSON]' => ['id' => $invoice['id'], 'type' => 1]]);
                        edk_admin(['receipt_check', 'NOK'],$message_id,$cid);
                        sm_admin(['send_reason_receipt'], ['back_panel_all']);
                        break;
                    case 'edit':
                        $db->update('transactions', ['status' => 1, 's_date' => time(), 'data[JSON]' => $decode], ['id' => $invoice['id']]);

                        admin_data(['step' => 'send_up_receipt', 'data' => $invoice['id']]);
                        edk_admin(['receipt_check', 'EDIT'],$message_id,$cid);
                        sm_admin(['send_up_receipt'], ['back_panel_all']);
                        break;
                    default:
                        
                        break;
                }
            } else {
                alert_admin(['checked_transaction']);
                $st = str_replace(['0','1'],['NOK','OK'],$invoice['status']);
                edk_admin(['receipt_check',$st],$message_id,$cid);
            }
        default:
            # code...
            break;
    }
}
