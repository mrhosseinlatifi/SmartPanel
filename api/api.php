<?php
class api
{
	protected $timeout = 30;
	protected $connect_timeout = 30;

	public function balance($api)
	{
		if ($api['smart_panel']) {
			$url = $api['api_url'];
			$data = ['key' => $api['api_key'], 'action' => 'balance'];
			$result = $this->rq($url, 'POST', $data);
			if ($result['result']) {
				$b = number_format($result['data']['balance']) . ' ' . strtoupper($result['data']['currency']) ?: 'Error';
				return ['result' => true, 'balance' => $b];
			} else {
				error_log('error balance : ' . $result['data']['error'] . ' - api : ' . $api['name']);
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
				error_log('error status : ' . $result['data']['error'] . ' - id : ' . $id . ' - api : ' . $api['name']);
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
				error_log('error status multi : ' . $result['data']['error'] . ' - id : ' . json_encode($ids) . ' - api : ' . $api['name']);
				return ['result' => false, 'error' => $result['data']['error']];
			}
		}
	}
	//---------------------------------------//
	public function add_order($api, $service, $link, $quantity, $comments = null)
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
			
			// Add comments if provided (array of comments joined with \r\n)
			if (!empty($comments) && is_array($comments)) {
				$postFields['comments'] = implode("\r\n", $comments);
			}
			
			$result = $this->rq($url, 'POST', $postFields);
			if ($result['result']) {
				return ['result' => true, 'order' => $result['data']['order']];
			} else {
				error_log('error add order : ' . $result['data']['error'] . ' - service : ' . $service . ' - api : ' . $api['name']);
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
			if ($result['result']) {
				return $result;
			} else {
				error_log('error services : ' . $result['data']['error'] . ' - api : ' . $api['name']);
				return ['result' => false, 'error' => $result['data']['error']];
			}
		}
	}

	private function rq($url, $method = 'GET', $data = [], $cookie_id = 'default')
	{
		if ($method == 'GET') {
			$q = http_build_query($data);
			$url .= '?' . $q;
		}

		$cookie_dir = ROOTPATH . '/temp/';
		if (!is_dir($cookie_dir)) {
			mkdir($cookie_dir, 0700, true);
		}

		$cookie_file = $cookie_dir . 'cookie_' . md5($cookie_id) . '.txt';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		}

		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
		]);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

		$response = curl_exec($ch);

		if (file_exists($cookie_file)) {
			chmod($cookie_file, 0600);
		}

		if (curl_errno($ch) || !$response) {
			error_log('error curl : code ' . curl_errno($ch) . ' txt : ' . curl_error($ch) . ' - url : ' . $url);
			return ['result' => false, 'data' => ['error' => curl_error($ch) ?: 'Empty response']];
		}

		$response = strtolower($response);
		$decoded = json_decode($response, true);

		if (is_null($decoded)) {
			error_log('error decoding JSON response: ' . substr($response, 0, 500) . ' - url: ' . $url);
			return ['result' => false, 'data' => ['error' => 'Invalid JSON or null response']];
		}


		$success = !isset($decoded['error']);

		return ['result' => $success, 'data' => $decoded];
	}
}
