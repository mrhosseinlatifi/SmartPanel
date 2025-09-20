<?php
function user_text()
{
    extract($GLOBALS);
    
    if ($text == '/start' or text_starts_with($text, "/start ")) {
        if (text_starts_with($text, "/start ")) {
            // Quick product deep link: /start product_{id}
            $startArg = str_replace('/start ', '', $text);
            if (text_starts_with($startArg, 'product_')) {
                $productId = str_replace('product_', '', $startArg);
                if (is_numeric($productId)) {
                    if ($tch['status'] == 'joined') {
                        $result_product = $db->get('products', '*', ['status' => 1, 'id' => (int)$productId]);
                        if ($result_product) {
                            // Build category path for proper navigation
                            $userdata = [];
                            $userdata['now'] = 0;
                            $userdata['category'] = [];
                            
                            // Build category hierarchy
                            $category_id = $result_product['category_id'];
                            $category_path = [];
                            
                            // Get full category path
                            while ($category_id) {
                                $category = $db->get('categories', '*', ['id' => $category_id]);
                                if ($category) {
                                    array_unshift($category_path, [
                                        'id' => $category['id'],
                                        'name' => json_decode($category['name']),
                                        'offset' => 0
                                    ]);
                                    $category_id = $category['category_id'];
                                    $userdata['now']++;
                                } else {
                                    break;
                                }
                            }
                            
                            $userdata['category'] = $category_path;
                            
                            // Calculate price with discounts
                            $price = $result_product['price'];
                            
                            // Apply product discount: positive = price increase, negative = price decrease
                            if ($result_product['discount']) {
                                $price = $price + (($result_product['price'] / 100) * $result_product['discount']);
                            }
                            
                            // Apply user discount (always reduces price)
                            if ($user['discount']) {
                                $price = $price - (($price / 100) * $user['discount']);
                            }
                            if ($price <= 0) {
                                $price = 0;
                                $price_once = price_once($result_product['min'], $result_product['max'], $price, $result_product['type']);
                                $how_much = $result_product['max'];
                            } else {
                                $price_once = price_once($result_product['min'], $result_product['max'], $price, $result_product['type']);
                                $how_much = $user['balance'] / $price_once;
                                if ($how_much >= $result_product['max']) {
                                    $how_much = $result_product['max'];
                                }
                            }
                            $userdata['product'] =  ['product' => $result_product['id'], 'min' => $result_product['min'], 'max' => $result_product['max'], 'price_once' => $price_once, 'pattern' => $result_product['pattern']];
                            
                            // Handle different product types
                            if ($result_product['type'] == 'custom_comments') {
                                // For comment type, show product first, then ask for comments
                                user_set_data(['step' => 'buy3', 'data[JSON]' => $userdata, 'type' => $result_product['type']]);
                                sm_user(['shop4_comments', $result_product, $price, $price_once, $result_product['min'], $result_product['max']], ['back_to_before']);
                            } elseif ($result_product['min'] == 1 && $result_product['max'] == 1) {
                                // For products with min/max = 1, show product info but ask for link directly
                                $userdata['price'] = $price_once;
                                $userdata['count'] = 1;
                                $link_text = $db->get('pattern', 'text', ['type' => $result_product['pattern']]);
                                user_set_data(['step' => 'buy5', 'data[JSON]' => $userdata, 'type' => $result_product['type']]);
                                sm_user(['shop4_direct_link', $result_product, $price, $price_once, $link_text], ['back_to_before']);
                            } else {
                                // Normal flow
                                user_set_data(['step' => 'buy3', 'data[JSON]' => $userdata, 'type' => $result_product['type']]);
                                sm_user(['shop4', $result_product, $price, $price_once, $how_much], ['back_to_before']);
                            }
                            return;
                        }
                    } else {
                        sm_user(['lock_channel', $tch['channels']], ['lock_channel', $tch['channels'], 0]);
                        exit;
                    }
                }
            }
            if ($section_status['main']['free']) {
                $referral_id = str_replace('/start ', '', $text);
                if (is_numeric($referral_id)) {
                    if ($fid != $referral_id) {
                        if ($db->has('users_information', ['user_id' => $referral_id])) {
                            if (!$db->has('users_information', ['user_id' => $fid])) {
                                if ($tch['status'] == 'joined') {
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
                                    sm_user(['lock_channel', $tch['channels']], ['lock_channel', $tch['channels'], $referral_id]);
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
    } elseif ($tch['status'] == 'joined') {
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
                    user_set_step('payment_selection');
                    sm_user(['payment_selection'], ['payment', $section_status]);
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
            sm_user(['lock_channel', $tch['channels']], ['lock_channel', $tch['channels'], 0]);
        }
        exit;
    }
}
