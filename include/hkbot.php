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
		$url = $this->url.'/'.$method;
		$response = $this->sendRequest($url, $data);
		if (isset($response['ok']) && $response['ok'] == 1) {
			return $response;
		} else {
			return ['ok' => false, 'desc' => $response['description']];
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

	public function sp($chatId, $file_id,$text, $replyMarkup = null)
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
		$result = $this->bot('getChatMember', $data)['result'];
		$result['user']['first_name'] = str_replace(['>', '/', '<'], '', $result['user']['first_name']);
		if(isset($result['user']['last_name'])){
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
		$status = $result['result']['status'];
		return $status;
	}
	/**
	 *  that is used to make HTTP requests to the API.
	 */
	private function sendRequest($url, $data)
	{
		$connection = curl_init($url);
		$options = array(
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_WHATEVER
		);
		curl_setopt_array($connection, $options);
		$response = curl_exec($connection);
		$error = curl_error($connection);
		curl_close($connection);
		
		if ($error) {
			error_log("Bot Class Curl Error : " . $error);
			return false; 
		} else {
			$decodedResponse = json_decode($response, true);
			return $decodedResponse;
		}
	}


}
