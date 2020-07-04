<?php
class ParsingFL extends Base {
	protected $tasks = array();

	// SimpleXMlElement root element
	protected $root = null;

	public function processing() {
		foreach(__URL__ as $url) {
			$this->logging(__CLASS__ . ' | URL: ' . $url);

			$this->initialize($url);

			if (is_object($this->root) && !is_null($this->root)) {
				$items = $this->root->channel->item;

				foreach ($items as $item) {
					$this->tasks[] = array(
						'title' => (string) $item->title,
						'link' => (string) $item->link,
						'description' => (string)$item->description,
						'category' => (string) $item->category,
						'pubDate' => $this->dateFormat((string) $item->pubDate, 'Y-m-d H:i:s'),
					);
				}
			} else {
				$this->logging(__CLASS__ . ' | Wrong initialized!');
			}
		}

		$this->logging(__CLASS__ . ' | Count parsed tasks: ' . count($this->tasks));

		return true;
	}

	public function getTasks() {
		return $this->tasks;
	}

	protected function initialize($url) {
		$this->root = null;
		
		$_data = $this->getUrlContent($url); // get content yml

		if (false !== $_data) {
			$this->root = simplexml_load_string($_data); // create object SimpleXMlElement
		} else {
			$this->logging(__CLASS__ . ' | content is empty');
		}
	}

	protected function getUrlContent($url) {
		$ch = curl_init();

		$options = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FOLLOWLOCATION => 1,
			CURLOPT_HEADER => false,
			CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36",
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		);

		curl_setopt_array($ch, $options);

		$content = curl_exec($ch);

		curl_close($ch);

		return $content;
	}

	protected function dateFormat($date, $format) {
		return date($format, strtotime($date));
	}
}