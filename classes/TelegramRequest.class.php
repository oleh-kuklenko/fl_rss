<?php
use GuzzleHttp\Client;

class TelegramRequest extends Base {
	// allowed methods for telegram API
	private $allowedApiMethods = [
		'sendMessage',
	];

	protected function query($method, $params = []) {
		if (empty($method)) {
			$this->logging(__CLASS__ . ' | Method is required!');

			throw new Exception('Method is required!');
		}

		if (!in_array($method, $this->allowedApiMethods)) {
			$this->logging(__CLASS__ . ' | Wrong is method!');

			throw new Exception('Wrong is method!');
		}

		$url = 'https://api.telegram.org/bot' . __TOKEN__ . '/' . $method;

		if (!empty($params)) {
			$url .= '?' . http_build_query($params);
		}

		try {
			$client = new Client([
				'base_uri' => $url,
			]);

			$request = $client->request('GET');

			return json_decode($request->getBody(), true);
		} catch (Exception $e) {
			$this->logging($e->getMessage());
		}
	}

}