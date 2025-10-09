<?php
function user_data()
{
	extract($GLOBALS);
	
	switch ($data) {
		case 'fyk':
			alert_user(['fyk']);
			break;
		case 'close':
			edt_user(['close']);
			break;
		case 'ozv':
			if ($tch['status'] == 'joined') {
				$bot->delete_msg($fid, $message_id);
				handleStart('start');
				alert_user(['joined']);
			} else {
				alert_user(['not_join'], true);
			}
			break;
		case 'support':
			alert_user(['none']);
			user_set_data(['step' => 'poshtibani2', 'data' => 'Reply again']);
			sm_user(['support'], ['back']);
			break;
		case text_starts_with($data, "ref_join"):
			if ($tch['status'] == 'joined') {
				$bot->delete_msg($fid, $message_id);
				$referral_id = str_replace('ref_join', '', $data);

				if (is_numeric($referral_id)) {
					if ($fid != $referral_id) {
						if ($db->has('users_information', ['user_id' => $referral_id])) {
							if (!$db->has('users_information', ['user_id' => $fid])) {
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
				alert_user(['not_join'], true);
			}

			break;
		case text_starts_with($data, "orderstatus_"):
			$str = str_replace('orderstatus_', '', $data);
			if ($db->has('orders', ['code' => $str])) {
				$res = $db->get('orders', '*', ['code' => $str]);
				alert_user(['result_order_inline', $res]);
			}
			break;
		case text_starts_with($data, "price_info_"):
			$str = str_replace('price_info_', '', $data);
			$ex = explode('_', $str);
			$type = $ex['0'];
			switch ($type) {
				case 'category':
					$type_2 = $ex['1'];
					switch ($type_2) {
						case 'show':
							$id = $ex['2'];
							$categoryResult = $db->get('categories', '*', ['id' => $id]);
							if ($categoryResult) {
								if ($db->has('categories', ['category_id' => $categoryResult['id']])) {
									# has under
									$result = get_category(['inline', 'offset' => 0], $categoryResult['id']);
									if ($result) {
										$c = $db->count('categories', ['status' => 1, 'category_id' => $categoryResult['id']]);
										edk_user(['price_info', $result, $c, $categoryResult['id'], 0]);
									} else {
										alert_user(['not_found']);
									}
								} else {
									# go show product
									if ($db->has('products', ['category_id' => $categoryResult['id']])) {
										# get products
										$result = get_products(['inline', 'offset' => 0], $categoryResult['id']);
										if ($result) {

											$c = $db->count('products', ['status' => 1, 'category_id' => $categoryResult['id']]);
											edt_user(['price_info'], ['price_info_products', $result, $c, $categoryResult['id'], 0]);
										} else {
											alert_user(['not_found']);
										}
									} else {
										alert_user(['not_found']);
									}
								}
							} else {
								alert_user(['not_found']);
							}
							break;
						case 'page':
							$depth = $ex['2'];
							$depth = ($depth == '0') ? null : $depth;
							$displaySettings = ($depth === null)
								? json_decode($settings['display_category'], true)
								: json_decode($settings['display_sub_category'], true);

							$page = $displaySettings['page'];
							$now = $ex['3'];
							$nex = $now + $page;
							$bef = $now - $page;

							$result = get_category(['inline', 'offset' => $now], $depth);
							$c = $db->count('categories', ['status' => 1, 'category_id' => $depth]);
							edk_user(['price_info', $result, $c, $depth, $now]);
							break;
						case 'back':
							$id = $ex['2'];
							$categoryResult = $db->get('categories', '*', ['id' => $id]);
							if ($categoryResult) {
								if ($categoryResult['category_id'] == null) {

									$displaySettings = json_decode($settings['display_category'], true);
									$depth = null;
									$now = 0;

									$result = get_category(['inline', 'offset' => $now], $depth);
									$c = $db->count('categories', ['status' => 1, 'category_id' => $depth]);
									edk_user(['price_info', $result, $c, $depth, $now]);
								} else {

									# has under
									$result = get_category(['inline', 'offset' => 0], $categoryResult['category_id']);
									if ($result) {
										$c = $db->count('categories', ['status' => 1]);
										edt_user(['price_info'], ['price_info', $result, $c, $categoryResult['category_id'], 0]);
									} else {
										alert_user(['not_found']);
									}
								}
							} else {
								alert_user(['not_found']);
							}

							break;
						default:
							# code...
							break;
					}
					break;
				case 'product':
					$type_2 = $ex['1'];
					switch ($type_2) {
						case 'show':
							$code = $ex['2'];
							$result_product = $db->get('products', '*', ['status' => 1, 'id' => $code]);
							if ($result_product) {
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

								edt_user(['price_info_products', $result_product, $price, $price_once, $how_much], ['product_info', $result_product]);
							} else {
								alert_user(['not_found']);
							}
							break;
						case 'back':
							$category_id = $ex['2'];
							$categoryResult = $db->get('categories', '*', ['id' => $category_id]);
							if ($categoryResult) {
								# get products
								$result = get_products(['inline', 'offset' => 0], $categoryResult['id']);
								if ($result) {
									$c = $db->count('products', ['status' => 1, 'category_id' => $categoryResult['id']]);
									edt_user(['price_info'], ['price_info_products', $result, $c, $categoryResult['id'], 0]);
								} else {
									alert_user(['not_found']);
								}
							} else {
								alert_user(['not_found']);
							}
							break;
						case 'page':
							$depth = $ex['2'];
							$now = $ex['3'];

							$result = get_products(['inline', 'offset' => $now], $depth);
							$c = $db->count('products', ['status' => 1, 'category_id' => $depth]);
							edt_user(['price_info'], ['price_info_products', $result, $c, $depth, $now]);
							break;
						case 'order':
							if ($section_status['main']['buy']) {
							$name_e = $ex['2'];

							$result_product = $db->get('products', '*', ['status' => 1, 'id' => $name_e]);

							if ($result_product) {
								$bot->delete_msg($fid, $message_id);
								
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
							} else {
								sm_user(['shop_justkey']);
							}
						}else{
							alert_user(['off_buy']);
						}
							break;
					}
					break;
				default:
					# code...
					break;
			}
			break;
		case text_starts_with($data, "backcoin_"):
			$code = str_replace('backcoin_', '', $data);

			$result = $db->has('payment_gateways', ['status' => 1]);
			if ($result) {
				$getTr = $db->get('transactions', '*', ['id' => $code]);
				$code = $getTr['id'];
				$text = $getTr['amount'];

				$decode = json_decode($getTr['data'], true);
				$paymentType = 'IRT';
				if (isset($decode['payment_type'])) {
					$paymentType = $decode['payment_type'];
				}

				$result = $db->select('payment_gateways', '*', ['status' => 1, 'type' => $paymentType]);

				edt_user(['payment_text', $user, $code, $text], ['payment_gateways', $result, $text, $domin, $code]);
				user_set_step();
			}
			break;
		case text_starts_with($data, "gift_code_"):
			$id = str_replace('gift_code_', '', $data);
			user_set_data(['step' => 'gift_code', 'link' => $message_id, 'data' => $id]);
			edt_user(['gift_code'], ['back_to_payment', $id]);
			break;
		case text_starts_with($data, "userpayment_"):
			$page = str_replace('userpayment_', '', $data);
			$n = $settings['last_transactions_page'];
			$nex = $page + $n;
			$bef = $page - $n;

			$result = $db->select('transactions', '*', ['user_id' => $fid, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [$page, $n], 'type' => 'payment']);
			if ($result) {
				$c = $db->count('transactions', ['user_id' => $fid, 'type' => 'payment']);
				$tx = transactionslist($result);
				edt_user(['last_payment', $tx], ['last_payment', $c,  $n, $bef, $nex]);
			} else {
				edt_user(['not_payment']);
			}

			break;
		case text_starts_with($data, "userorder_"):
			$page = str_replace('userorder_', '', $data);
			$n = $settings['last_order_page'];
			$nex = $page + $n;
			$bef = $page - $n;

			$result = $db->select('orders', '*', ['user_id' => $fid, 'ORDER' => ['id' => 'DESC'], 'LIMIT' => [$page, $n]]);
			if ($result) {
				$c = $db->count('orders', ['user_id' => $fid]);
				$tx = orderlist($result);
				edt_user(['last_order', $tx], ['last_order', $c,  $n, $bef, $nex]);
			} else {
				edt_user(['not_order']);
			}
			break;
		case text_starts_with($data, "send_receipt_"):
			$amount = str_replace('send_receipt_', '', $data);
			user_set_data(['step' => 'payment_offline_2', 'data' => $amount]);
			$bot->delete_msg($fid, $message_id);
			sm_user(['send_receipt_photo',$amount], ['back']);
			break;
		default:
			# code...
			break;
	}
}
