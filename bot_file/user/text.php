<?php
function user_text()
{
    extract($GLOBALS);

    if ($text == '/start' or text_starts_with($text, "/start ")) {
        if (text_starts_with($text, "/start ")) {
            if ($section_status['main']['free']) {
                $referral_id = str_replace('/start ', '', $text);
                if (is_numeric($referral_id)) {
                    if ($fid != $referral_id) {
                        if ($db->has('users_information', ['user_id' => $referral_id])) {
                            if (!$db->has('users_information', ['user_id' => $fid])) {
                                if ($tch == 'joined') {
                                    if (!$section_status['free']['number']) {

                                        processReferral($referral_id, $fid);
                                        handleStart('start');
                                    } else {
                                        $db->insert('users_information', ['user_id' => $fid, 'step' => 'oknum', 'referral_id' => $referral_id . 'off', 'join_date' => time()]);
                                        sm_user(['ok_commission', $settings['gift_payment'], $referral_id, $fid], null, $referral_id);
                                        sm_user(['referral_authentication'], ['request_contact']);
                                        StartGift($fid);
                                    }
                                } else {
                                    sm_user(['lock_channel', $settings['channel_lock']], ['lock_channel', $settings['channel_lock'], $referral_id]);
                                }
                            } else {
                                handleStart('start');
                            }
                        } else {
                            handleStart('start');
                        }
                    } else {
                        handleStart('start');
                    }
                } else {
                    handleStart('start');
                }
            } else {
                handleStart('start');
            }
        } else {
            handleStart('start');
        }
    } elseif ($tch == 'joined') {
        switch ($text) {
            case $key['back']:
                handleStart('back');
                break;
            case in_array($text, $key['price']):
                $result = get_category(['inline', 'offset' => 0], null);
                if ($result) {
                    $c = $db->count('categories', ['status' => 1, 'category_id' => null]);
                    sm_user(['price_info'], ['price_info', $result, $c, 0, 0]);
                } else {
                    sm_user(['not_category']);
                }
                break;
            case in_array($text, $key['order']):
                if ($section_status['main']['buy']) {
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
                } else {
                    sm_user(['off_buy']);
                }
                break;
            case in_array($text, $key['info']):
                if ($section_status['main']['free']) {
                    if ($section_status['free']['gift_referral']  and $section_status['free']['gift_payment']) {

                        $tx = ['info', 2, $first_name, $last_name, $user_name, $user];
                    } elseif ($section_status['free']['gift_payment']) {

                        $tx = ['info', 3, $first_name, $last_name, $user_name, $user];
                    } elseif ($section_status['free']['gift_referral']) {

                        $tx = ['info', 4, $first_name, $last_name, $user_name, $user];
                    } else {

                        $tx = ['info', 1, $first_name, $last_name, $user_name, $user];
                    }
                } else {

                    $tx = ['info', 1, $first_name, $last_name, $user_name, $user];
                }

                sm_user($tx, ['home']);
                break;
            case in_array($text, $key['payment']):
                if ($section_status['main']['payment']) {
                    if ($section_status['payment']['offline_payment']) {
                        user_set_step('payment_selection');
                        sm_user(['payment_selection'], ['payment', $section_status]);
                    } else {
                        if ($user['payment_card'] == 'wait') {
                            sm_user(['wait_verify_card']);
                        } else {
                            if (($user["number"] == 0 or text_contains($user['number'], 'off')) and $section_status['payment']['number']) {
                                user_set_step('contact');

                                sm_user(['payment_authentication'], ['request_contact']);
                            } else {
                                user_set_step('payment_selection');
                                sm_user(['payment_selection'], ['payment', $section_status]);
                            }
                        }
                    }
                } else {
                    sm_user(['off_payment'], ['home']);
                }
                break;
            case in_array($text, $key['support']):
                if ($section_status['main']['support']) {
                    if ($user['ticket'] < $settings['ticket']) {
                        user_set_step('poshtibani1');
                        sm_user(['reason_support'], ['support_panel']);
                    } else {
                        sm_user(['pending_support'], ['home']);
                    }
                } else {
                    sm_user(['off_support']);
                }
                break;
            case in_array($text, $key['status']):
                user_set_step('status_menu');
                sm_user(['status'], ['status']);
                break;
            case in_array($text, $key['free']):
                if ($section_status['main']['free'] && ($section_status['free']['gift_referral']  or $section_status['free']['gift_payment'])) {
                    $bot->sp($fid, new CURLFile(ROOTPATH . "/baner.jpg"), $media->text('baner_tx', [$settings['baner_tx'], $fid]));
                    if ($section_status['free']['gift_referral']  and $section_status['free']['gift_payment']) {
                        $tx = ['referral_text', [1, $settings['gift_referral'], $settings['gift_payment']]];
                    } elseif ($section_status['free']['gift_referral']  and !$section_status['free']['gift_payment']) {
                        $tx = ['referral_text', [2, $settings['gift_referral']]];
                    } elseif (!$section_status['free']['gift_referral']  and $section_status['free']['gift_payment']) {
                        $tx = ['referral_text', [3, $settings['gift_payment']]];
                    }

                    user_set_step('free_menu');
                    sm_user($tx, ['gift']);
                } else {
                    sm_user(['off_referral'], ['home']);
                }
                break;
            case $key['panel_admin']:
                if ($admin) {
                    admin_data(['step' => 'none', 'data' => 'none']);
                    user_set_step('admin');
                    sm_admin(['start_panel'], ['home', $access]);
                } else {
                    sm_user(['error_text_key'], ['home']);
                }
                break;
            default:
                sm_user(['error_text_key'], ['home']);
                break;
        }
    } else {
        if (!empty($fid)) {
            if (!$db->has('users_information', ['user_id' => $fid])) {
                $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'join_date' => time()]);
                StartGift($fid);
            } else {
                user_set_step();
            }
            sm_user(['lock_channel', $settings['channel_lock']], ['lock_channel', $settings['channel_lock'], 0]);
        }
        exit;
    }
}
