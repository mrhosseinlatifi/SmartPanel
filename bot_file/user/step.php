<?php

function user_step()
{
    extract($GLOBALS);
    switch ($step) {
        case 'oknum':
            if ($text == $key['back']) {
                handleStart('back');
            } else {
                if (isset($message['contact'])) {
                    $contact = $message['contact'];
                    $contact_id = $contact['user_id'];
                    $contact_number = $contact['phone_number'];

                    $referral_id = str_replace('off', '', $user['referral_id']);

                    if ($contact_id == $fid) {

                        if (mb_strpos($contact_number, '98') === 0 || mb_strpos($contact_number, '+98') === 0) {
                            $contactuser = '0' . strrev(substr(strrev($contact_number), 0, 10));

                            if (!$db->has('users_information', ['number' => [$contactuser, $contactuser . 'off']])) {
                                if ($section_status['free']['sms']) {
                                    $last_sms = json_decode($user['last_sms'], 1);
                                    $lastmsg = $last_sms['last_sms'];
                                    $code = $last_sms['c'];
                                    $n = $lastmsg + $settings['delay_time_sms'];
                                    if ($n <= time()) {

                                        $sms = sendsms($settings['user_sms'], $settings['pas_sms'], $settings['ptid_ref'], $settings['from_number'], $contact_number);
                                        if ($sms['result'] == 'OK') {
                                            user_set_data(['step' => 'sendsms', 'last_sms[JSON]' => ['last_sms' => time(), 'c' => md5($sms['code'])], 'number' => $contactuser . 'off']);

                                            sm_user(['referral_authentication_sms', $fid, $contactuser], ['back']);
                                        } else {

                                            sm_channel('channel_errors', ['sms_error', json_encode($sms['error'])]);
                                            sm_user(['again_referral_authentication']);
                                        }
                                    } else {
                                        user_set_step('sendsms');
                                        sm_user(['delay_time_sms', $n],['back']);
                                    }
                                } else {

                                    processReferral($referral_id, $fid);

                                    handleStart('start');
                                }
                            } else {
                                sm_user(['number_is_db'], ['request_contact']);
                            }
                        } else {
                            sm_user(['not_iran_contact'], ['request_contact']);
                        }
                    } else {
                        sm_user(['only_key_use_contact'], ['request_contact']);
                    }
                } else {
                    sm_user(['only_key_use_contact'], ['request_contact']);
                }
            }
            return;
            break;
        case 'sendsms':
            $text = convertnumber($text);
            if (is_numeric($text)) {
                $text = md5($text);
                $last_sms = json_decode($user['last_sms'], 1);
                $lastmsg = $last_sms['last_sms'];
                $code = $last_sms['c'];
                if ($text == $code) {
                    $contactuser = str_replace('off', '', $user['number']);
                    $referral_id = str_replace('off', '', $user['referral_id']);
                    if ($section_status['payment']['authentication']) {
                        sm_channel('channel_kyc', ['ok_number_2', $contactuser, $fid, $first_name, $referral_id]);
                    }

                    user_set_data(['step' => 'none' , 'referral_id' => $referral_id, 'number' => $contactuser]);
                    processReferral($referral_id, $fid);
                    handleStart('start');
                } else {
                    sm_user(['wrong_code_referral_authentication']);
                }
            } else {
                sm_user(['int_code_referral_authentication']);
            }
            break;
    }
    switch ($text) {
        case '/start':
        case $key['back']:
            handleStart('back');
            return;
            break;
    }
    if ($tch == 'joined') {
        switch ($step) {
            case 'poshtibani1':
                if (in_array($text, $key['support_panel'])) {
                    user_set_data(['step' => 'poshtibani2', 'data' => $text]);
                    sm_user(['support'], ['back']);
                } else {
                    sm_user(['use_bot_keyboards'], ['support_panel']);
                }
                break;

            case 'poshtibani2':
                $flattenedArray = flattenArray($key);
                if (!in_array($text, $flattenedArray)) {
                    if (isset($text)) {
                        sm_channel('channel_support', ['admin_support', $fid, $first_name, $user['data'], $text], ['admin_support', $fid]);
                        user_set_data(['step' => 'none', 'data' => null, 'ticket[+]' => 1]);
                        sm_user(['ok_support'], ['home']);
                    } else {
                        $result = handlePoshtibani2($update, $media, $settings['channel_support'], $bot, $fid, $first_name, $user, $db);
                        if ($result) {
                            user_set_data(['step' => 'none', 'data' => null, 'ticket[+]' => 1]);
                            sm_user(['ok_support'], ['home']);
                        } else {
                            sm_user(['send_msg']);
                        }
                    }
                }
                break;
            case 'status_menu':
                switch ($text) {
                    case in_array($text, $key['last_order']):
                        $page = $settings['last_order_page'];
                        $result = $db->select('orders', '*', ['user_id' => $fid, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [0, $page]]);
                        if ($result) {
                            $c = $db->count('orders', ['user_id' => $fid]);
                            $tx = orderlist($result);
                            sm_user(['last_order', $tx], ['last_order', $c,  $page, -1, $page]);
                        } else {
                            sm_user(['not_order']);
                        }
                        break;
                    case in_array($text, $key['last_payment']):
                        $page = $settings['last_transactions_page'];
                        $result = $db->select('transactions', '*', ['user_id' => $fid, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [0, $page], 'type' => 'payment']);
                        if ($result) {
                            $c = $db->count('transactions', ['user_id' => $fid, 'type' => 'payment']);
                            $tx = transactionslist($result);
                            sm_user(['last_payment', $tx], ['last_payment', $c,  $page, -1, $page]);
                        } else {
                            sm_user(['not_payment']);
                        }
                        break;
                    case in_array($text, $key['status_order']):
                        user_set_step('status_order');
                        sm_user(['order_code'], ['back_to_before']);
                        break;
                }
                break;
            case 'status_order':
                if ($text == $key['back_to_before']) {
                    user_set_step('status_menu');
                    sm_user(['status'], ['status']);
                } else {
                    if ($db->has('orders', ['OR' => ['id' => $text, 'code_api' => $text], 'user_id' => $fid])) {
                        $info_order = $db->get('orders', '*', ['OR' => ['id' => $text, 'code_api' => $text], 'user_id' => $fid]);
                        user_set_step('status_menu');
                        sm_user(['info_order_status', $info_order], ['status']);
                    } else {
                        sm_user(['wrong_code']);
                    }
                }
                break;
            case 'payment_selection':
                switch ($text) {
                    case $key['payment_offline']:
                        if ($section_status['main']['payment']  && $section_status['payment']['offline_payment']) {
                            sm_user(['card_be_card', $settings['p2p']]);
                        } else {
                            sm_user(['off_payment'], ['payment', $section_status]);
                        }
                        break;
                    case $key['payment_online']:
                        if ($section_status['main']['payment']) {
                            if ($user['payment_card'] == 'wait') {
                                sm_user(['wait_verify_card']);
                            } else {
                                if (($user["number"] == 0 || text_contains($user['number'], 'off')) and $section_status['payment']['number']) {
                                    user_set_step('contact');
                                    sm_user(['payment_authentication'], ['request_contact']);
                                } else {
                                    user_set_step('up_balance');
                                    sm_user(['payment_amount', $settings['min_deposit'], $settings['max_deposit']], ['back']);
                                }
                            }
                        } else {
                            sm_user(['off_payment'], ['home']);
                        }
                        break;
                    case $key['move_balance']:
                        if ($section_status['payment']['move_balance']) {
                            if ($settings['min_move_balance'] <= $user['balance']) {
                                user_set_step('move_1');
                                sm_user(['move_balance', $settings['min_move_balance']], ['back']);
                            } else {
                                sm_user(['min_move_balance', $settings['min_move_balance']]);
                            }
                        } else {
                            sm_user(['off_move_balance', $settings['min_move_balance']]);
                        }
                        break;
                    case $key['charge_code']:
                        user_set_step('charge_1');
                        sm_user(['charge_code_input'], ['back']);
                        break;
                }
                break;
            case 'charge_1':
                $result = $db->get('gift_code', '*', ['status' => 1, 'code' => $text, 'type' => 'fix']);
                if ($result) {
                    $ids = json_decode($result['ids'], true);
                    if (!in_array($fid, $ids)) {
                        // ok_charge_code
                        $decode = json_decode($result['amount'], true);
                        $old_balance = $user['balance'];
                        $new_balance = $user['balance'] + $decode['amount'];
                        insertTransaction('gift_code', $fid, $old_balance, $new_balance, $decode['amount'], 'charge');
                        $db->update('users_information', ['balance[+]' => $decode['amount']], ['user_id' => $fid]);
                        $ids[] = $fid;
                        if (($result['count'] - 1) == 0) {
                            $db->update('gift_code', ['status' => 0, 'count' => 0, 'ids[JSON]' => $ids], ['id' => $result['id']]);
                        } else {
                            $db->update('gift_code', ['count[-]' => 1, 'ids[JSON]' => $ids], ['id' => $result['id']]);
                        }
                        user_set_step();
                        sm_user(['ok_charge_code', $decode['amount']], ['home']);
                    } else {
                        // already_charge_code
                        sm_user(['already_charge_code']);
                    }
                } else {
                    // wrong_charge_code
                    sm_user(['wrong_charge_code']);
                }
                break;
            case 'move_1':
                if (is_numeric($text) && $text != $fid) {
                    $result = $db->get('users_information', '*', ['user_id' => $text]);
                    if ($result) {
                        user_set_data(['step' => 'move_2', 'data[JSON]' => ['id' => $result['user_id'], 'amount' => 0]]);
                        sm_user(['move_balance_1']);
                    } else {
                        sm_user(['move_balance_not_found']);
                    }
                } else {
                    sm_user(['move_balance_int']);
                }
                break;
            case 'move_2':
                if (is_numeric($text)) {
                    if ($user['balance'] >= $text && $settings['min_move_balance'] <= $text) {
                        $decode = json_decode($user['data'], true);
                        user_set_data(['step' => 'move_3', 'data[JSON]' => ['id' => $decode['id'], 'amount' => $text]]);

                        $name = $bot->getChatMember($decode['id'])['user']['first_name'];

                        sm_user(['move_balance_2', $name, $decode['id'], $text], ['ok_move_balance']);
                    } else {
                        sm_user(['move_balance_not_enough', $settings['min_move_balance']]);
                    }
                } else {

                    sm_user(['move_balance_int_2']);
                }
                break;
            case 'move_3':
                if ($text == $key['ok_move_balance']) {
                    // move_balance
                    $decode = json_decode($user['data'], true);
                    $fid2 = $decode['id'];
                    $amount = $decode['amount'];
                    $sender = $db->get('users_information', '*', ['user_id' => $fid]);
                    $reciver = $db->Get('users_information', '*', ['user_id' => $fid2]);

                    $new_balance = $reciver['balance'] + $amount;
                    insertTransaction('receive_balance', $reciver['user_id'], $reciver['balance'], $new_balance, $amount, $sender['user_id']);
                    $db->update('users_information', ['balance[+]' => $amount], ['user_id' => $reciver['user_id']]);

                    $new_balance = $sender['balance'] + $amount;
                    $db->update('users_information', ['balance[-]' => $amount], ['user_id' => $sender['user_id']]);
                    insertTransaction('send_balance', $sender['user_id'], $sender['balance'], $new_balance, $amount, $reciver['user_id']);

                    sm_user(['receive_balance', $amount, $sender['user_id']], null, $reciver['user_id']);
                    user_set_step();
                    sm_user(['ok_move_balance'], ['home']);
                }
                break;
            case 'free_menu':
                if ($section_status['main']['free']) {
                    switch ($text) {
                        case $key['balance']:
                            if ($section_status['free']['gift_referral'] or $section_status['free']['gift_payment']) {
                                if ($section_status['free']['gift_referral']  and $section_status['free']['gift_payment']) {
                                    $tx = ['info_referral', 1, $first_name, $last_name, $user_name, $user, $idbot];
                                } elseif ($section_status['free']['gift_referral']  and !$section_status['free']['gift_payment']) {
                                    $tx = ['info_referral', 2, $first_name, $last_name, $user_name, $user, $idbot];
                                } elseif (!$section_status['free']['gift_referral']  and $section_status['free']['gift_payment']) {
                                    $tx = ['info_referral', 3, $first_name, $last_name, $user_name, $user, $idbot];
                                }
                                sm_user($tx);
                            } else {
                                sm_user(['off_referral']);
                            }
                            break;
                        case $key['change_gift_balance']:
                            if ($section_status['free']['change_gift_balance']) {
                                if ($user['gift'] >= $settings['min_move_gift']) {
                                    user_set_step('change_gift_balance_1');
                                    sm_user(['amount_gift_balance', $settings['min_move_gift'], $user['gift']], ['back']);
                                } else {
                                    sm_user(['min_gift_balance', $settings['min_move_gift']]);
                                }
                            } else {
                                sm_user(['off_referral']);
                            }
                            break;
                        case $key['withdraw_balance']:
                            if ($section_status['free']['withdraw_balance']) {
                                if ($user['gift'] >= $settings['min_payment_gift']) {
                                    user_set_step('withdraw_balance_1');
                                    sm_user(['amount_withdraw_balance', $settings['min_payment_gift'], $user['gift']], ['back']);
                                } else {
                                    sm_user(['min_withdraw_balance', $settings['min_payment_gift']]);
                                }
                            } else {
                                sm_user(['off_referral']);
                            }
                            break;
                    }
                } else {
                    sm_user(['off_referral']);
                }
                break;
            case 'withdraw_balance_1':
                $text = convertnumber($text);
                if (is_numeric($text) and $text >= $settings['min_payment_gift'] and $text <= $user['gift']) {
                    user_set_data(['step' => 'withdraw_balance_2', 'data' => $text]);
                    sm_user(['info_withdraw_balance']);
                } else {
                    sm_user(['amount_withdraw_balance_wrong', $settings['min_payment_gift'], $user['gift']]);
                }
                break;
            case 'withdraw_balance_2':
                $text = convertnumber($text);
                $req = $user['data'];

                $old_balance = $user['balance'];
                $new_balance = $user['balance'] - $req;

                $db->insert('transactions', ['status' => 2, 'user_id' => $fid, 'type' => 'gift_payout', 'amount' => $req, 'data[JSON]' => ['info' => $text, 'old' => $old_balance, 'new' => $new_balance], 'date' => time(), 'tracking_code' => 0, 'getway' => 'gift']);
                $code  = $db->id();
                if (is_numeric($code)) {
                    user_set_data(['step' => 'none', 'data' => 'none', 'gift[-]' => $req]);
                    sm_channel('channel_gift_transaction', ['ok_gift_out', $code, $fid, $first_name, $req, $text], ['gifts_payouts', $code]);
                    sm_user(['ok_withdraw_balance', $code], ['home']);
                } else {
                    user_set_data(['step' => 'none', 'data' => 'none']);
                    sm_user(['error_withdraw_balance', $code], ['home']);
                }
                break;
            case 'change_gift_balance_1':
                $text = convertnumber($text);
                if (is_numeric($text)) {
                    if ($text >= $settings['min_move_gift'] and $text <= $user['gift']) {
                        $old_balance = $user['balance'];
                        $new_balance = $user['balance'] + $text;
                        insertTransaction('gift_move', $fid, $old_balance, $new_balance, $text, 'gift');
                        user_set_data(['step' => 'none', 'data' => 'none', 'gift[-]' => $text, 'balance[+]' => $text]);
                        sm_user(['ok_gift_balance', $text], ['home']);
                    } else {
                        sm_user(['amount_gift_balance_wrong', $min_to_move_balance, $user['gift']]);
                    }
                } else {
                    sm_user(['gift_balance_int']);
                }
                break;
            case 'buy1':
                $userdata = json_decode($user['data'], 1);

                if ($text == $key['back_to_before']) {

                    unset($userdata['category'][count($userdata['category']) - 1]);
                    $userdata['now'] -= 1;
                    if ($userdata['now'] < 0) {
                        handleStart('back');
                        return;
                    } elseif ($userdata['now'] == '0') {
                        $last_category['id'] = $idc = null;
                        $offset = $userdata['category']['0']['offset'];
                    } else {
                        $last_category = end($userdata['category']);
                        $idc = $last_category['id'];
                        $offset = $last_category['offset'];
                    }

                    $result = get_category(['offset' => $offset], $idc);

                    if ($result) {

                        $c = $db->count('categories', ['status' => 1, 'category_id' => $last_category['id']]);
                        user_set_data(['step' => 'buy1', 'data[JSON]' => $userdata]);
                        sm_user(['shop1'], ['shop_keyboard', $result, $c, $idc, $offset]);
                    } else {

                        sm_user(['shop_justkey']);
                    }
                } else {
                    if ($text == $key['next_page'] || $text == $key['prev_page']) {

                        $now = $userdata['now'];

                        $last_category = end($userdata['category']);
                        $offset = $last_category['offset'];
                        if ($userdata['now'] == '0') {
                            $idc = null;
                            $last_category['name'] = '';
                            $displaySettings = json_decode($settings['display_category'], true);
                        } else {
                            $idc = $last_category['id'];
                            $last_category['name'] = '';
                            $displaySettings = json_decode($settings['display_sub_category'], true);
                        }

                        if ($text == $key['next_page']) {
                            $str = $offset + $displaySettings['page'];
                        } else {
                            $str = $offset - $displaySettings['page'];
                        }
                        $last_category['offset'] = $str;

                        $result = get_category(['offset' => $str], $idc);
                        if ($result) {
                            $c = $db->count('categories', ['status' => 1, 'category_id' => $idc]);

                            $userdata['category'][count($userdata['category']) - 1] = $last_category;

                            user_set_data(['step' => 'buy1', 'data[JSON]' => $userdata]);

                            sm_user(['shop1'], ['shop_keyboard', $result, $c, $idc, $str]);
                        } else {
                            sm_user(['not_category']);
                        }
                    } else {
                        // Get FindByCategory
                        $name_e = js($text);
                        // Find category id
                        if ($userdata['now'] == '0') {
                            $idc = null;
                        } else {
                            $last_category = end($userdata['category']);
                            $idc = $last_category['id'];
                        }

                        $result_categorys = $db->get('categories', '*', ['status' => 1, 'name' => $name_e, 'category_id' => $idc]);
                        if ($result_categorys) {

                            $result = get_category(['offset' => 0], $result_categorys['id']);

                            // under menu first
                            if ($result) {
                                $userdata['now'] += 1;
                                $userdata['category'][] = ["offset" => 0, "id" => $result_categorys['id'], 'name' => $result_categorys['name']];
                                $c = $db->count('categories', ['status' => 1, 'category_id' => $result_categorys['id']]);

                                user_set_data(['step' => 'buy1', 'data[JSON]' => $userdata]);

                                sm_user(['shop2', $result_categorys['name']], ['shop_keyboard', $result, $c, 'under', -1]);
                            } else {
                                $result = get_products(['offset' => 0], $result_categorys['id']);
                                if ($result) {

                                    $c = $db->count('products', ['status' => 1, 'category_id' => $result_categorys['id']]);

                                    $userdata['now'] += 1;
                                    $userdata['category'][] = ["offset" => 0, "id" => $result_categorys['id'], 'name' => $result_categorys['name']];
                                    $userdata['product_offset'] = 0;

                                    user_set_data(['step' => 'buy2', 'data[JSON]' => $userdata]);

                                    sm_user(['shop3', $result_categorys['name']], ['shop_keyboard', $result, $c, 'product', -1]);
                                } else {
                                    // not category
                                    sm_user(['not_category']);
                                }
                            }
                        } else {
                            sm_user(['shop_justkey']);
                        }
                    }
                }
                break;
            case 'buy2':
                $userdata = json_decode($user['data'], 1);

                if ($text == $key['back_to_before']) {

                    unset($userdata['category'][count($userdata['category']) - 1]);
                    $userdata['now'] -= 1;
                    if ($userdata['now'] == '0') {
                        $idc = null;
                        $offset = $userdata['category']['0']['offset'];
                        $last_category['name'] = '';
                    } else {
                        $last_category = end($userdata['category']);
                        $idc = $last_category['id'];
                        $offset = $last_category['offset'];
                        $last_category['name'] = '';
                    }
                    $result = get_category(['offset' => $offset], $idc);
                    // under menu first
                    if ($result) {

                        $c = $db->count('categories', ['status' => 1, 'category_id' => $idc]);

                        user_set_data(['step' => 'buy1', 'data[JSON]' => $userdata]);

                        sm_user(['shop2', $last_category['name']], ['shop_keyboard', $result, $c, $idc, $offset]);
                    } else {

                        sm_user(['shop_justkey']);
                    }
                } else {
                    if ($text == $key['next_page'] || $text == $key['prev_page']) {

                        $last_category = end($userdata['category']);
                        $offset = $last_category['offset'];
                        $displaySettings = json_decode($settings['display_products'], true);

                        if ($text == $key['next_page']) {
                            $str = $offset + $displaySettings['page'];
                        } else {
                            $str = $offset - $displaySettings['page'];
                        }

                        $last_category['offset'] = $str;

                        $result = get_products(['offset' => $str], $last_category['id']);
                        if ($result) {

                            $c = $db->count('products', ['status' => 1, 'category_id' => $last_category['id']]);

                            $userdata['category'][count($userdata['category']) - 1] = $last_category;

                            user_set_data(['step' => 'buy2', 'data[JSON]' => $userdata]);

                            sm_user(['shop3', $last_category['name']], ['shop_keyboard', $result, $c, 'product', $str]);
                        } else {
                            // not category
                            sm_user(['not_category']);
                        }
                    } else {
                        $name_e = js($text);

                        $last_category = end($userdata['category']);

                        $result_product = $db->get('products', '*', ['status' => 1, 'category_id' => $last_category['id'], 'name' => $name_e]);


                        if ($result_product) {
                            $dis = 0;
                            if ($result_product['discount']) {
                                $dis += $result_product['discount'];
                            }
                            if ($user['discount']) {
                                $dis += $user['discount'];
                            }
                            $price = $result_product['price'] - (($result_product['price'] / 100) * ($dis));
                            if ($price <= 0) {
                                $price = 0;
                                $price_once = price_once($result_product['min'], $result_product['max'], $price);
                                $how_much = $result_product['max'];
                            } else {
                                $price_once = price_once($result_product['min'], $result_product['max'], $price);
                                $how_much = $user['balance'] / $price_once;
                                if ($how_much >= $result_product['max']) {
                                    $how_much = $result_product['max'];
                                }
                            }
                            $userdata['product'] =  ['product' => $result_product['id'], 'min' => $result_product['min'], 'max' => $result_product['max'], 'price_once' => $price_once, 'pattern' => $result_product['pattern']];
                            user_set_data(['step' => 'buy3', 'data[JSON]' => $userdata, 'type' => $result_product['type']]);
                            sm_user(['shop4', $result_product, $price, $price_once, $how_much], ['back_to_before']);
                        } else {
                            sm_user(['shop_justkey']);
                        }
                    }
                }
                break;
            case 'buy3':
                $userdata = json_decode($user['data'], 1);
                if ($text == $key['back_to_before']) {
                    if (isset($userdata['category'])) {
                        $last_category = end($userdata['category']);

                        $result = get_products(['offset' => $last_category['offset']], $last_category['id']);
                        if ($result) {

                            $c = $db->count('products', ['status' => 1, 'category_id' => $last_category['id']]);

                            user_set_data(['step' => 'buy2']);

                            sm_user(['shop3', $last_category['name']], ['shop_keyboard', $result, $c, 'product', -1]);
                        } else {
                            // not category
                            sm_user(['not_category']);
                        }
                    } else {
                        $result = get_category(['offset' => 0], null);
                        if ($result) {
                            $c = $db->count('categories', ['status' => 1, 'category_id' => null]);
                            $userdata['now'] = 0;
                            $userdata['category'][] = ["offset" => 0, "id" => 0];

                            user_set_data(['step' => 'buy1', 'data[JSON]' => $userdata]);

                            sm_user(['shop1'], ['shop_keyboard', $result, $c, null, -1]);
                        } else {
                            sm_user(['not_category']);
                        }
                    }
                } else {
                    $text = convertnumber($text);
                    if (is_numeric($text) and ($text >= $userdata['product']["min"] and $text <= $userdata['product']["max"])) {
                        $a2 = $text * $userdata['product']['price_once'];
                        if ($user['balance'] >= $a2) {
                            $link_text = $db->get('pattern', 'text', ['type' => $userdata['product']['pattern']]);
                            $userdata['price'] = $a2;
                            $userdata['count'] = $text;
                            user_set_data(['step' => 'buy5', 'data[JSON]' => $userdata]);
                            sm_user(['link_text', $link_text]);
                        } else {
                            user_set_step();
                            sm_user(['low_balance', $a2, $user], ['home']);
                        }
                    } else {
                        sm_user(['shop_range', $userdata['product']["min"], $userdata['product']["max"]]);
                    }
                }
                break;
            case 'buy5':
                $userdata = json_decode($user['data'], 1);
                if ($text == $key['back_to_before']) {

                    $name_e = $userdata['product']['product'];

                    $last_category = end($userdata['category']);

                    $result_product = $db->get('products', '*', ['status' => 1, 'id' => $name_e]);


                    if ($result_product) {
                        $dis = 0;
                        if ($result_product['discount']) {
                            $dis += $result_product['discount'];
                        }
                        if ($user['discount']) {
                            $dis += $user['discount'];
                        }
                        $price = $result_product['price'] - (($result_product['price'] / 100) * ($dis));
                        if ($price <= 0) {
                            $price = 0;
                            $price_once = price_once($result_product['min'], $result_product['max'], $price);
                            $how_much = $result_product['max'];
                        } else {
                            $price_once = price_once($result_product['min'], $result_product['max'], $price);
                            $how_much = $user['balance'] / $price_once;
                            if ($how_much >= $result_product['max']) {
                                $how_much = $result_product['max'];
                            }
                        }
                        $userdata['product'] =  ['product' => $result_product['id'], 'min' => $result_product['min'], 'max' => $result_product['max'], 'price_once' => $price_once, 'pattern' => $result_product['pattern']];
                        user_set_data(['step' => 'buy3', 'data[JSON]' => $userdata, 'type' => $result_product['type']]);
                        sm_user(['shop4', $result_product, $price, $price_once, $how_much], ['back_to_before']);
                    } else {
                        sm_user(['shop_justkey']);
                    }
                } else {
                    $result_product = $db->get('products', '*', ['id' => $userdata['product']['product']]);
                    $pattern = $db->get('pattern', 'pattern', ['type' => $userdata['product']['pattern']]);
                    if (preg_match($pattern, $text, $matches)) {
                        user_set_data(['step' => 'sefaresh', 'link' => $matches[0]]);

                        sm_user(['shop5', $result_product, $userdata["count"], $matches[0], $userdata["price"]], ['ok_order']);
                    } else {
                        sm_user(['shop_just_en']);
                    }
                }
                break;
            case 'sefaresh':
                $userdata = json_decode($user['data'], 1);
                if ($text == $key['back_to_before']) {
                    /**
                     * Back Last Menu
                     */
                    user_set_step('buy5');
                    $link_text = $db->get('pattern', 'text', ['type' => $userdata['product']['pattern']]);
                    sm_user(['link_text', $link_text], ['back_to_before']);
                } elseif ($text == $key['cancel_order']) {
                    /**
                     * Cancel Order
                     */
                    user_set_data(['step' => 'none', 'data' => 'none']);
                    sm_user(['cancel_order'], ['home']);
                } elseif ($text == $key['ok_order']) {
                    /**
                     * OK order
                     */
                    if ($section_status['main']['buy']) {
                        /**
                         * Check 1 day order
                         */
                        $user['last_msg'] = json_decode($user['last_msg'], 1)['last_msg'];
                        if ($user['last_msg'] <= $user['last_msg'] + 86400) {
                            /**
                             * Check Balance
                             */
                            if ($user['balance'] >= $userdata['price']) {
                                // add To database
                                $product_id = $userdata['product']['product'];
                                $result_product = $db->get('products', '*', ['id' => $product_id, 'status' => 1]);

                                if ($result_product) {
                                    if ($result_product['api'] == 'noapi') {
                                        /**
                                         * No API orders
                                         */
                                        // insert orders
                                        $db->insert('orders', [
                                            'status' => 'pending',
                                            'user_id' => $fid,
                                            'price' => $userdata['price'],
                                            'count' => $userdata['count'],
                                            'product[JSON]' => ['category' => getCategoryHierarchy($result_product['category_id'], true), 'product' => json_decode($result_product['name'])],
                                            'link' => $user['link'],
                                            'date' => time(),
                                            'api' => 'noapi',
                                            'code_api' => 0,
                                            'extra_data[JSON]' =>
                                            [
                                                'product' =>
                                                [
                                                    'id' => $product_id,
                                                    'service_id' => $result_product['service'],
                                                ]
                                            ]
                                        ]);
                                        $code = $db->id();
                                        if ($code) {
                                            $old_balance = $user['balance'];
                                            $new_balance = $user['balance'] - $userdata['price'];

                                            insertTransaction('orders', $fid, $old_balance, $new_balance, $userdata['price'], 'buy');
                                            // balance
                                            $old_balance = $user['balance'];
                                            user_set_data([
                                                'balance[-]' => $userdata['price'],
                                                'number_order[+]' => 1,
                                                'amount_spent[+]' => $userdata['price'],
                                                'step' => 'none',
                                                'link' => 'none',
                                                'data[JSON]' => [],
                                            ]);
                                            $new_balance = $old_balance - $userdata['price'];

                                            sm_channel(
                                                'channel_order_noapi',
                                                [
                                                    'order_receipt',
                                                    getCategoryHierarchy($result_product['category_id']),
                                                    $result_product['name'],
                                                    $first_name,
                                                    $fid,
                                                    $userdata['price'],
                                                    $userdata['count'],
                                                    $user["link"],
                                                    $code,
                                                    $old_balance,
                                                    $new_balance,
                                                    'noapi'
                                                ],
                                                ['order_noapi', $code, 'pending']
                                            );
                                            // chanel ok order
                                            if ($section_status['buy']['order_msg'] && $settings['channel_ads']) {
                                                sm_channel('channel_ads', ['order_receipt_channel', getCategoryHierarchy($result_product['category_id']), $result_product['name'], $userdata['count'], $userdata['price'], $idbot], ['successful_order', $idbot]);
                                            }
                                            // edit and resid
                                            sm_user(['order_receipt_ok', $user["link"], $userdata['count'], $userdata['price'], $code, json_decode($result_product['name'])]);
                                            sm_user(['text_order', $settings['text_order']], ['home']);
                                        } else {
                                            // error insert
                                            $tx = json_encode($db->errorInfo, 448);
                                            sm_channel('channel_errors', ['db_error', $result_product['name'], $fid, $tx]);
                                            user_set_step();
                                            sm_user(['error_order'], ['home']);
                                        }
                                    } else {
                                        /**
                                         * API ORDERS
                                         */
                                        // insert orders
                                        $api_info = $db->get('apis', '*', ['name' => $result_product['api'], 'status' => 1]);
                                        if ($api_info) {
                                            if ($result_product['confirm']) {
                                                /**
                                                 * Need Confirmation
                                                 */
                                                $db->insert('orders', [
                                                    'status' => 'waiting',
                                                    'user_id' => $fid,
                                                    'price' => $userdata['price'],
                                                    'count' => $userdata['count'],
                                                    'product[JSON]' => ['category' => getCategoryHierarchy($result_product['category_id'], true), 'product' => json_decode($result_product['name'])],
                                                    'link' => $user['link'],
                                                    'date' => time(),
                                                    'api' => $api_info['name'],
                                                    'code_api' => 0,
                                                    'extra_data[JSON]' =>
                                                    [
                                                        'product' =>
                                                        [
                                                            'id' => $product_id,
                                                            'service_id' => $result_product['service'],
                                                        ]
                                                    ]
                                                ]);

                                                $code = $db->id();
                                                if ($code) {
                                                    // balance
                                                    $old_balance = $user['balance'];
                                                    $new_balance = $user['balance'] - $userdata['price'];

                                                    user_set_data([
                                                        'balance[-]' => $userdata['price'],
                                                        'number_order[+]' => 1,
                                                        'amount_spent[+]' => $userdata['price'],
                                                        'step' => 'none',
                                                        'link' => 'none',
                                                        'data[JSON]' => [],
                                                    ]);


                                                    insertTransaction('orders', $fid, $old_balance, $new_balance, $userdata['price'], 'buy');
                                                    sm_channel(
                                                        'channel_order_api',
                                                        [
                                                            'order_receipt',
                                                            getCategoryHierarchy($result_product['category_id']),
                                                            $result_product['name'],
                                                            $first_name,
                                                            $fid,
                                                            $userdata['price'],
                                                            $userdata['count'],
                                                            $user["link"],
                                                            $code,
                                                            $old_balance,
                                                            $new_balance,
                                                            $api_info['name']
                                                        ],
                                                        ['order_api_confirm', $code, 'pending']
                                                    );

                                                    // chanel ok order
                                                    if ($section_status['buy']['order_msg'] && $settings['channel_ads']) {
                                                        sm_channel('channel_ads', ['order_receipt_channel', getCategoryHierarchy($result_product['category_id']), $result_product['name'], $userdata['count'], $userdata['price'], $idbot], ['successful_order', $idbot]);
                                                    }
                                                    // edit and resid
                                                    sm_user(['order_receipt_ok', $user["link"], $userdata['count'], $userdata['price'], $code, json_decode($result_product['name'])]);
                                                    sm_user(['text_order', $settings['text_order']], ['home']);
                                                } else {
                                                    // error insert
                                                    $tx = json_encode($db->errorInfo, 448);
                                                    sm_channel('channel_errors', ['db_error', $result_product['name'], $fid, $tx]);
                                                    user_set_step();
                                                    sm_user(['error_order'], ['home']);
                                                }
                                            } else {
                                                /**
                                                 * Default API ORDERS
                                                 */
                                                $db->insert('orders', [
                                                    'status' => 'pending',
                                                    'user_id' => $fid,
                                                    'price' => $userdata['price'],
                                                    'count' => $userdata['count'],
                                                    'product[JSON]' => ['category' => getCategoryHierarchy($result_product['category_id'], true), 'product' => json_decode($result_product['name'])],
                                                    'link' => $user['link'],
                                                    'date' => time(),
                                                    'api' => $api_info['name'],
                                                    'code_api' => 0,
                                                    'extra_data[JSON]' =>
                                                    [
                                                        'product' =>
                                                        [
                                                            'id' => $product_id,
                                                            'service_id' => $result_product['service'],
                                                        ]
                                                    ]
                                                ]);

                                                $code = $db->id();
                                                if ($code) {
                                                    // balance
                                                    $old_balance = $user['balance'];
                                                    $new_balance = $user['balance'] - $userdata['price'];
                                                    user_set_data([
                                                        'balance[-]' => $userdata['price'],
                                                        'number_order[+]' => 1,
                                                        'amount_spent[+]' => $userdata['price'],
                                                        'step' => 'none',
                                                        'link' => 'none',
                                                        'data[JSON]' => [],
                                                    ]);
                                                    insertTransaction('orders', $fid, $old_balance, $new_balance, $userdata['price'], 'buy');

                                                    sm_channel(
                                                        'channel_order_api',
                                                        [
                                                            'order_receipt',
                                                            getCategoryHierarchy($result_product['category_id']),
                                                            $result_product['name'],
                                                            $first_name,
                                                            $fid,
                                                            $userdata['price'],
                                                            $userdata['count'],
                                                            $user["link"],
                                                            $code,
                                                            $old_balance,
                                                            $new_balance,
                                                            'noapi'
                                                        ],
                                                        ['order_api', $code, 'pending']
                                                    );

                                                    // chanel ok order
                                                    if ($section_status['buy']['order_msg'] && $settings['channel_ads']) {
                                                        sm_channel('channel_ads', ['order_receipt_channel', getCategoryHierarchy($result_product['category_id']), $result_product['name'], $userdata['count'], $userdata['price']], ['successful_order']);
                                                    }
                                                    // edit and resid
                                                    sm_user(['order_receipt_ok', $user["link"], $userdata['count'], $userdata['price'], $code, json_decode($result_product['name'])]);
                                                    sm_user(['text_order', $settings['text_order']], ['home']);
                                                } else {
                                                    // error insert
                                                    $tx = json_encode($db->errorInfo, 448);
                                                    sm_channel('channel_errors', ['db_error', $result_product['name'], $fid, $tx]);
                                                    user_set_step();
                                                    sm_user(['error_order'], ['home']);
                                                }
                                            }
                                        } else {
                                            sm_user(['off_buy']);
                                        }
                                    }
                                } else {
                                    user_set_step();
                                    sm_channel('channel_errors', ['product_error', $result_product['name'], $fid, $tx]);
                                    sm_user(['error_order'], ['home']);
                                }
                            } else {
                                user_set_step();
                                sm_user(['low_balance', $a2, $user], ['home']);
                            }
                        } else {
                            // 1 day 
                            user_set_step();
                            sm_user(['error_order'], ['home']);
                        }
                    } else {
                        sm_user(['off_buy']);
                    }
                }
                break;
            case 'contact':
                if (isset($message['contact'])) {
                    $contact = $message['contact'];
                    $contact_id = $contact['user_id'];
                    $contact_number = $contact['phone_number'];

                    if ($contact_id == $fid) {
                        if (strpos($contact_number, '98') !== false || strpos($contact_number, '+98') !== false) {
                            $contactuser = '0' . strrev(substr(strrev($contact_number), 0, 10));
                            if ($section_status['payment']['sms']) {
                                $decode = json_decode($user['last_sms'], 1);
                                $lastmsg = $decode['last_sms'];
                                $code = $decode['c'];
                                $n = $lastmsg + $settings['delay_time_sms'];
                                if ($n <= time()) {

                                    $sms = sendsms($settings['user_sms'], $settings['pas_sms'], $settings['ptid_payment'], $settings['from_number'], $contact_number);

                                    if ($sms['result'] == 'OK') {
                                        user_set_data(['step' => 'send_sms', 'last_sms[JSON]' => ['last_sms' => time(), 'c' => md5($sms['code'])], 'number' => $contactuser . 'off']);
                                        sm_user(['payment_authentication_send_sms'],['back']);
                                    } else {
                                        sm_channel('channel_errors', ['sms_error', json_encode($sms['error'])]);
                                        sm_user(['payment_authentication_send_sms_again']);
                                    }
                                } else {
                                    user_set_step('send_sms');
                                    sm_user(['delay_time_sms', $n],['back']);
                                }
                            } else {
                                # Send Data To Channel
                                if ($section_status['payment']['authentication']) {
                                    sm_channel('channel_kyc', ['ok_number', $contactuser, $fid, $first_name]);
                                }
                                if (text_contains($user['referral_id'], 'off') && $user['referral_id']) {
                                    $str = str_replace('off', '', $user['referral_id']);
                                    user_set_data(['step' => 'payment_selection', 'number' => $contactuser, 'referral_id' => $str]);
                                } else {
                                    user_set_data(['step' => 'payment_selection', 'number' => $contactuser]);
                                }

                                sm_user(['payment_selection'], ['payment', $section_status]);
                            }
                        } else {
                            sm_user(['payment_authentication_not_iran']);
                        }
                    } else {
                        sm_user(['payment_authentication_wrong_number']);
                    }
                } else {
                    sm_user(['payment_authentication_just_key']);
                }
                break;
            case 'send_sms':
                $text = convertnumber($text);
                if (is_numeric($text)) {
                    $decode = json_decode($user['last_sms'], 1);
                    $lastmsg = $decode['last_sms'];
                    $code = $decode['c'];
                    if (md5($text) == $code) {
                        $contactuser = str_replace('off', '', $user['number']);

                        if (text_contains($user['referral_id'], 'off') !== false) {
                            $str = str_replace('off', '', $user['referral_id']);
                            user_set_data(['step' => 'payment_selection', 'number' => $contactuser, 'referral_id' => $str]);
                        } else {
                            user_set_data(['step' => 'payment_selection', 'number' => $contactuser]);
                        }

                        if ($section_status['payment']['authentication']) {
                            sm_channel('channel_kyc', ['ok_number', $contactuser, $fid, $first_name]);
                        }

                        sm_user(['payment_selection'], ['payment', $section_status]);
                    } else {
                        sm_user(['payment_authentication_wrong_code']);
                    }
                } else {
                    sm_user(['payment_authentication_int']);
                }
                break;
            case 'up_balance':
                $text = convertnumber($text);
                if (is_numeric($text)) {
                    if ($text >= $settings['min_deposit'] && $text <= $settings['max_deposit']) {
                        if ($section_status['payment']['verify_card'] and !$user['payment_card'] and $text >= $settings['min_kyc']) {
                            user_set_step('verify_card');
                            sm_user(['verify_msg', $settings['text_kyc']], ['back']);
                        } else {
                            $result = $db->has('payment_gateways', ['status' => 1]);
                            if ($result) {
                                if ($db->has('transactions', ['amount' => $text, 'status' => 2, 'tracking_code' => 0, 'type' => 'payment', 'user_id' => $fid])) {
                                    $code = $db->get('transactions', 'id', ['amount' => $text, 'status' => 2, 'tracking_code' => 0, 'type' => 'payment', 'user_id' => $fid]);
                                    if ($code) {
                                        $result = $db->select('payment_gateways', '*', ['status' => 1]);

                                        sm_user(['payment_text', $user, $code, $text], ['payment_gateways', $result, $text, $domin, $code]);
                                        user_set_step();

                                        if ($user['payment_card'] > 0) {
                                            sm_user(['just_card', $user['payment_card']]);
                                        }

                                        sm_user(['back_pay', $settings['text_payment']], ['home']);
                                    } else {
                                        user_set_step();
                                        sm_user(['payment_mistake'], ['home']);
                                    }
                                } else {
                                    $code = add_tranaction('payment', $fid, $text);

                                    if ($code) {
                                        $result = $db->select('payment_gateways', '*', ['status' => 1]);

                                        sm_user(['payment_text', $user, $code, $text], ['payment_gateways', $result, $text, $domin, $code]);
                                        user_set_step();

                                        if ($user['payment_card'] > 0) {
                                            sm_user(['just_card', $user['payment_card']]);
                                        }

                                        sm_user(['back_pay', $settings['text_payment']], ['home']);
                                    } else {
                                        user_set_step();
                                        sm_user(['payment_mistake'], ['home']);
                                    }
                                }
                            } else {
                                user_set_step();
                                sm_channel('channel_errors', ['off_all_payments']);
                                sm_user(['payment_mistake'], ['home']);
                            }
                        }
                    } else {
                        sm_user(['payment_int']);
                    }
                } else {
                    sm_user(['payment_int']);
                }
                break;

            case 'gift_code':
                if ($db->has('gift_code', ['code' => $text, 'count[>=]' => 1, 'status' => 1, 'type' => 'percent'])) {
                    $code = $user['data'];
                    $result = $db->has('payment_gateways', ['status' => 1]);
                    if ($result) {
                        $getTr = $db->get('transactions', '*', ['id' => $code]);

                        $decode = json_decode($getTr['data'], true);
                        $decode['discount'] = $text;
                        $db->update('transactions', ['data[JSON]' => $decode], ['id' => $getTr['id']]);

                        $code = $getTr['id'];

                        $result = $db->select('payment_gateways', '*', ['status' => 1]);
                        $bot->delete_msg($fid, $message_id);
                        edt_user(['payment_text', $user, $code, $getTr['amount'], $text], ['payment_gateways', $result, $getTr['amount'], $domin, $code], $fid, $user['link']);
                        user_set_step();
                    }
                } else {
                    sm_user(['wrong_gift_code']);
                }
                break;
            case 'verify_card':
                if (isset($update['message']['photo'])) {
                    $photo = $update['message']['photo'];
                    $caption = $update['message']['caption'] ?? '';
                    $file_id = end($photo)['file_id'];
                    if($settings['channel_kyc'] != 0){
                        $bot->sp($settings['channel_kyc'], $file_id, $media->atext('payment_verify_caption', [$fid, $first_name, $caption]), $media->akeys('verify_keys', $fid));
                    }else{
                        $bot->sp(admins['0'], $file_id, $media->atext('payment_verify_caption', [$fid, $first_name, $caption]), $media->akeys('verify_keys', $fid));
                    }
                    user_set_data(['step' => 'none', 'payment_card' => 'wait']);
                    sm_user(['send_verify_ok'], ['home']);
                } else {
                    sm_user(['vetify_just_photo']);
                }
                break;
            default:
                # code...
                break;
        }
    } else {
        if (!$db->has('users_information', ['user_id' => $fid])) {
            $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'join_date' => time()]);
            StartGift($fid);
        } else {
            user_set_step();
        }
        sm_user(['lock_channel', $settings['channel_lock']], ['lock_channel', $settings['channel_lock'], 0]);
    }
}
