<?php
class api
{

	public function balance($api)
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$data = ['key' => $api['api_key'], 'action' => 'balance'];
			$result = $this->rq($url, 'POST', $data);
			if ($result['result']) {
				$b = number_format($result['data']['balance']) . ' ' . strtoupper($result['data']['currency']) ?: 'مشکل در دریافت';
				return ['result' => true, 'balance' => $b];
			} else {
				error_log('error balance : ' . $result['data']['error'].' - api : '. $api['name']);
				return ['result' => false, 'balance' => 'Error'];
			}
		}
	}
	//---------------------------------------//

	public function status($api, $id)
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$postFields = [
				'action' => 'status',
				'key' => $api['api_key'],
				'order' => $id,
			];
			$result = $this->rq($url, 'POST', $postFields);

			if ($result['result']) {
				return ['result' => true, 'data' => $result['data']];
			} else {
				error_log('error status : ' . $result['data']['error'].' - id : '. $id.' - api : '. $api['name']);
				return ['result' => false, 'error' => $result['data']['error']];
			}
		}
	}

	public function status_multi($api, $ids = [])
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$postFields = [
				'action' => 'status',
				'key' => $api['api_key'],
				'orders' => implode(',', $ids),
			];
			$result = $this->rq($url, 'POST', $postFields);

			if ($result['result']) {
				return ['result' => true, 'data' => $result['data']];
			} else {
				error_log('error status multi : ' . $result['data']['error'].' - id : '. $id.' - api : '. $api['name']);
				return ['result' => false, 'error' => $result['data']['error']];
			}
		}
	}
	//---------------------------------------//
	public function add_order($api, $service, $link, $quantity)
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$postFields = [
				'action' => 'add',
				'key' => $api['api_key'],
				'service' => $service,
				'link' => $link,
				'quantity' => $quantity,
			];
			$result = $this->rq($url, 'POST', $postFields);
			if ($result['result']) {
				return ['result' => true, 'order' => $result['data']['order']];
			} else {
				error_log('error add order : ' . $result['data']['error'].' - service : '. $service.' - api : '. $api['name']);
				return ['result' => false, 'error' => $result['data']['error']];
			}
		}
	}
	//---------------------------------------//
	public function services($api)
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$data = ['key' => $api['api_key'], 'action' => 'services'];
			$result = $this->rq($url, 'POST', $data);

			return $result;
		}
	}

	function rq($url, $method = 'GET', $data = [])
	{
		if ($method == 'GET') {
			$q = http_build_query($data);
			$url .= '?' . $q;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			return ['result' => false, 'data' => ['error' => curl_error($ch)]];
		} else {
			$result = strtolower($result);
			$result = json_decode($result, true);
			if (!isset($result['error'])) {
				$result_type = true;
			} else {
				$result_type = false;
			}
			return ['result' => $result_type, 'data' => $result];
		}
	}
}
