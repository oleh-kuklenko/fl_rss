<?php

class TelegramSender extends TelegramRequest {

	public function formatting($data) {
		$html  = "<a href='" . $data['link'] . "'>" . htmlspecialchars($data['title']) . "</a>\n\n";
        $html .= "Категория: <b>" . $data['category'] . "</b>\n";
        $html .= "Дата: <b>" . $data['date'] . "</b>";

        return $html;
	}

	public function send($message) {
		if ($message) {
            $response = $this->query('sendMessage', array(
                'chat_id' => __CHAT_ID__,
                'text' => $message,
                'parse_mode' => 'HTML'
            ));

			return $response;
		}

        return false;
	}

}