<?php
class hkbot
{
	private $token;
	private $url;

	public function __construct($token)
	{
		$this->token = $token;
		$this->url = "https://api.telegram.org/bot" . $token;
	}

	public function bot($method, $data = null)
	{
		$url = $this->url . '/' . $method;
		$response = $this->sendRequest($url, $data);
		if ($response === false) {
			return ['ok' => false, 'result' => null, 'desc' => 'Network connection error. Please check your internet connection and try again.'];
		}
		if (!is_array($response)) {
			return ['ok' => false, 'result' => null, 'desc' => 'Invalid response from Telegram API'];
		}
		if (isset($response['ok']) && $response['ok'] == 1) {
			return $response;
		} else {
			$description = isset($response['description']) ? $response['description'] : 'Unknown error';
			return ['ok' => false, 'result' => null, 'desc' => $description];
		}
	}

	public function sm($chatId, $text, $replyMarkup = null)
	{
		$replyMarkup = isset($replyMarkup) ? json_encode($replyMarkup) : null;
		$data = array(
			'chat_id' => $chatId,
			'text' => $text,
			'parse_mode' => 'HTML',
			'disable_web_page_preview' => true,
			'reply_markup' => $replyMarkup
		);
		return $this->bot('sendMessage', $data);
	}

	public function sp($chatId, $file_id, $text, $replyMarkup = null)
	{
		$replyMarkup = isset($replyMarkup) ? json_encode($replyMarkup) : null;
		$data = array(
			'chat_id' => $chatId,
			'photo' => $file_id,
			'caption' => $text,
			'parse_mode' => 'HTML',
			'reply_markup' => $replyMarkup
		);
		return $this->bot('sendphoto', $data);
	}

	public function edit_keyboard($chatId, $messageId, $replyMarkup)
	{
		$replyMarkup = isset($replyMarkup) ? json_encode($replyMarkup) : null;
		$data = array(
			'chat_id' => $chatId,
			'message_id' => $messageId,
			'reply_markup' => $replyMarkup
		);
		return $this->bot('editMessageReplyMarkup', $data);
	}

	public function edit_text($chatId, $messageId, $text, $replyMarkup = null)
	{
		$replyMarkup = isset($replyMarkup) ? json_encode($replyMarkup) : null;
		$data = array(
			'chat_id' => $chatId,
			'message_id' => $messageId,
			'text' => $text,
			'parse_mode' => 'HTML',
			'disable_web_page_preview' => true,
			'reply_markup' => $replyMarkup
		);
		return $this->bot('editMessageText', $data);
	}

	public function alert($callbackQueryId, $text = null, $showAlert = false)
	{
		$data = array(
			'callback_query_id' => $callbackQueryId,
			'text' => $text,
			'show_alert' => $showAlert,
		);
		return $this->bot('answerCallbackQuery', $data);
	}

	public function delete_msg($chatId, $messageId)
	{
		$data = array(
			'chat_id' => $chatId,
			'message_id' => $messageId
		);
		$this->bot('deleteMessage', $data);
	}

	public function getChatMember($userId)
	{
		$data = array(
			'chat_id' => $userId,
			'user_id' => $userId
		);
		$response = $this->bot('getChatMember', $data);
		if (!isset($response['result']) || !is_array($response['result'])) {
			return ['user' => ['first_name' => 'Unknown', 'last_name' => '']];
		}
		$result = $response['result'];
		if (isset($result['user']['first_name'])) {
			$result['user']['first_name'] = str_replace(['>', '/', '<'], '', $result['user']['first_name']);
		} else {
			$result['user']['first_name'] = 'Unknown';
		}
		if (isset($result['user']['last_name']) && $result['user']['last_name'] !== null) {
			$result['user']['last_name'] = str_replace(['>', '/', '<'], '', $result['user']['last_name']);
		}
		return $result;
	}

	public function check_join($chatId, $channelId)
	{
		// Check if the channel name has a hyphen
		if (strpos($channelId, '-') !== false) {
			$result = $this->bot('getChatMember', [
				'chat_id' => $channelId,
				'user_id' => $chatId
			]);
		} else {
			$channelId = "@" . $channelId;
			$result = $this->bot('getChatMember', [
				'chat_id' => $channelId,
				'user_id' => $chatId
			]);
		}
		if (isset($result['result']['status'])) {
			$status = $result['result']['status'];
		} else {
			$status = 'left';
		}
		return $status;
	}
	/**
	 *  that is used to make HTTP requests to the API.
	 */
	private function sendRequest($url, $data)
	{
		$connection = curl_init($url);
		if ($connection === false) {
			error_log("Bot Class Curl Error : Failed to initialize curl");
			return false;
		}
		
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2
		);
		curl_setopt_array($connection, $options);
		$response = curl_exec($connection);
		$error = curl_error($connection);
		$httpCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
		curl_close($connection);

		if ($error) {
			$errorMsg = "Bot Class Curl Error : " . $error;
			if (strpos($error, 'getaddrinfo') !== false) {
				$errorMsg .= " (DNS resolution failed)";
			}
			error_log($errorMsg);
			return false;
		}
		
		if ($response === false || empty($response)) {
			error_log("Bot Class Curl Error : Empty or invalid response (HTTP Code: " . $httpCode . ")");
			return false;
		}
		
		$decodedResponse = json_decode($response, true);
		if ($decodedResponse === null && json_last_error() !== JSON_ERROR_NONE) {
			error_log("Bot Class Curl Error : JSON decode failed - " . json_last_error_msg() . " (Response: " . substr($response, 0, 200) . ")");
			return false;
		}
		
		return $decodedResponse;
	}
}
