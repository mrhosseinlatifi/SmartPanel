<?php
define('maindir', __DIR__);
require maindir . "/config.php";

//-----------------------------------//
require ROOTPATH . "/bot_file/function/function_tel.php";
require ROOTPATH . "/include/db.php";
require ROOTPATH . "/include/hkbot.php";
require ROOTPATH . "/include/jdf.php";
require ROOTPATH . '/api/api.php';
require ROOTPATH . "/media/index.php";

// last_cron_orders
// cron_order_lock
if (!get_option('cron_order_lock', 1)) {
	update_option('cron_order_lock', 1);
	$show_channel = get_option('channel_main', 0);
	// send pendign orders
	$api = new api();
	$media = new media;
	$bot = new hkbot(Token);
	$getBotInfo = $bot->bot('getMe');
	$numberId = $getBotInfo['result']['id'];
	$idbot = $getBotInfo['result']['username'];
	define('DIFF_TIME', get_diff_time());

	$result_multi_apis = $db->select('apis', '*', ['multi' => 1, 'smart_panel' => 1]);
	$checked_apis = null;
	if ($result_multi_apis) {
		foreach ($result_multi_apis as $api_multi) {
			$checked_apis[] = $api_multi['name'];
			$ord_multi_check = null;
			$ord_multi_check = $db->select("orders", '*', ['api' => $api_multi['name'], 'OR' => ['status' => ['pending', 'in progress']], 'LIMIT' => get_option('limit_multi', 50), 'code_api[!]' => 0]);
			$ids = [];
			if ($ord_multi_check) {
				foreach ($ord_multi_check as $co) {
					$now = (time() - $co['date']) / 86400;
					if ($now <= 15) {
						$ids[] = $co['code_api'];
					}
				}
				$res_api = $api->status_multi($api_multi, $ids);
				if ($res_api['result']) {
					foreach ($res_api['data'] as $order => $val) {
						$fd = $db->get('orders', '*', ['code_api' => $order, 'api' => $api_multi['name']]);
						$user_id = $fd['user_id'];
						if (isset($val['status'])) {
							switch ($val['status']) {
								case 'pending':
									# code...
									break;
								case 'in progress':
									if ($fd['status'] == 'pending') {
										$db->update('orders', ['status' => 'in progress'], ['id' => $fd['id']]);
										sm_user(['order_go', $fd, $show_channel], null, $user_id);
									}
									break;
								case 'completed':
									$db->update('orders', ['status' => 'completed'], ['id' => $fd['id']]);
									sm_user(['order_confirmation', $fd, $show_channel], null, $user_id);
									break;
								case 'canceled':
								case 'refunded':
									$usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
									$old_balance = $usResult['balance'];
									$new_balance = $old_balance + $fd['price'];
									insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $fd['price'], $fd['id']);

									$db->update('orders', ['status' => 'canceled'], ['id' => $fd['id']]);
									$db->update('users_information', ['balance[+]' => $fd['price'], 'amount_spent[-]' => $fd['price']], ['user_id' => $user_id]);
									sm_user(['order_cancel', $fd, $show_channel], null, $user_id);
									break;
								case 'partial':
									$usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
									$old_balance = $usResult['balance'];
									$new_balance = $old_balance + $fd['price'];
									insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $fd['price'], $fd['id']);
									$val['remains'] = (isset($val['remains']) && $val['remains'] > 0) ? $val['remains'] : 0;
									$back = (($val['remains'] * $fd['price']) / $fd['count']);
									$db->update('orders', ['status' => 'partial'], ['id' => $fd['id']]);
									$db->update('users_information', ['balance[+]' => $back, 'amount_spent[-]' => $back], ['user_id' => $user_id]);
									sm_user(['order_partial', $fd, $back, $show_channel], null, $user_id);
									break;
								default:
									# code...
									break;
							}
						} else {
							$decode_data = json_decode($fd['extra_data'], true);
							$decode_data['error_check'] = 'Error';
							$db->update('orders', ['status' => 'error', 'extra_data[JSON]' => $decode_data], ['id' => $fd['id']]);
						}
					}
				}
			}
		}
	} else {
		if ($checked_apis === null) {
			$s = ['noapi'];
		} else {
			$s = ['noapi', $checked_apis];
		}

		$orders_singel_check = $db->select('orders', '*', ['api[!]' => $s, 'OR' => ['status' => ['pending', 'in progress']], 'LIMIT' => get_option('limit', 100), 'code_api[!]' => 0]);
		if ($orders_singel_check) {
			foreach ($orders_singel_check as $order) {
				$now = (time() - $order['date']) / 86400;
				if ($now >= 15) {
					$db->update('orders', ['status' => 'unknow'], ['id' => $order['id']]);
				} else {
					$api_info = $db->get('apis', '*', ['name' => $order['api']]);
					if ($api_info) {
						$result_order = $api->status($api_info, $order['code_api']);
						if ($result_order['result']) {
							$user_id = $order['user_id'];
							switch ($result_order['data']['status']) {
								case 'pending':
									# code...
									break;
								case 'in progress':
									if ($order['status'] == 'pending') {
										$db->update('orders', ['status' => 'in progress'], ['id' => $order['id']]);
										sm_user(['order_go', $order, $show_channel], null, $user_id);
									}
									break;
								case 'completed':
									$db->update('orders', ['status' => 'completed'], ['id' => $order['id']]);
									sm_user(['order_confirmation', $order, $show_channel], null, $user_id);
									break;
								case 'canceled':
								case 'refunded':
									$usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
									$old_balance = $usResult['balance'];
									$new_balance = $old_balance + $order['price'];
									insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $order['price'], $fd['id']);

									$db->update('orders', ['status' => 'canceled'], ['id' => $order['id']]);
									$db->update('users_information', ['balance[+]' => $order['price'], 'amount_spent[-]' => $order['price']], ['user_id' => $user_id]);
									sm_user(['order_cancel', $order, $show_channel], null, $user_id);
									break;
								case 'partial':
									$usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
									$old_balance = $usResult['balance'];
									$new_balance = $old_balance + $order['price'];
									insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $order['price'], $fd['id']);

									$back = (($result_order['data']['remains'] * $order['price']) / $order['count']);
									$db->update('orders', ['status' => 'partial'], ['id' => $order['id']]);
									$db->update('users_information', ['balance[+]' => $back, 'amount_spent[-]' => $back], ['user_id' => $user_id]);
									sm_user(['order_partial', $order, $back, $show_channel], null, $user_id);
									break;
								default:
									# code...
									break;
							}
						}
					}
				}
			}
		}
	}

	$result_add_order = $db->select("orders", '*', ['status' => 'pending', 'api[!]' => 'noapi', 'code_api' => 0, 'LIMIT' => 50]);
	foreach ($result_add_order as $order) {
		$now = (time() - $order['date']) / 86400;
		if ($now >= 15) {
			$db->update('orders', ['status' => 'unknow'], ['id' => $order['id']]);
		} else {
			$api_info = $db->get('apis', '*', ['name' => $order['api']]);
			if ($api_info) {
				$link = $order['link'];
				$count = $order['count'];
				$decode_data = json_decode($order['extra_data'], true);
				$user_id = $order['user_id'];
				$service = $decode_data['product']['service_id'];

				$add_info = $api->add_order($api_info, $service, $link, $count);
				if ($add_info['result']) {
					$db->update('orders', ['code_api' => $add_info['order']], ['id' => $order['id']]);
				} else {
					$usResult = $db->get('users_information', '*', ['user_id' => $user_id]);
					$old_balance = $usResult['balance'];
					$new_balance = $old_balance + $order['price'];
					insertTransaction('orders_back', $user_id, $old_balance, $new_balance, $order['price'], $order['id']);

					$decode_data['error'] = (isset($add_info['error'])) ? $add_info['error'] : 'Error';
					$db->update('orders', ['status' => 'error', 'extra_data[JSON]' => $decode_data], ['id' => $order['id']]);
					$db->update('users_information', ['balance[+]' => $order['price'], 'amount_spent[-]' => $order['price']], ['user_id' => $user_id]);
					sm_user(['order_cancel', $order, $show_channel], null, $user_id);
					sm_channel('channel_errors', ['order_add_error', $decode_data['error']]);
				}
			}
		}
	}

	update_option('cron_order_lock', 0);
	update_option('last_cron_orders', time());
} else {
	echo 'Lock';
	$now = time();
	$last = get_option('last_cron_orders', time()) + 120;
	if ($now >= $last) {
		update_option('cron_order_lock', 0);
		update_option('last_cron_orders', time());
	}
}
