<?php
class Base {
	protected $logging = null;

	protected $enableLogging = true;

	protected $timestamp = null;

	public function logging($message) {
		if (!$this->logging && $this->enableLogging) {
			if (class_exists('Log')) {
				$this->logging = new Log('processing.log');
			} else {
				$this->enableLogging = false;
			}
		}

		if($this->logging) {
			if(!$this->timestamp) {
				$this->timestamp = (int)microtime(true);
			}

			$this->logging->write($this->timestamp . ' | ' . $message);
		}
	}

	protected function translit($text) { 
		$ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я"); 
		
		$en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

	 	$res = str_replace($ru, $en, $text);
		$res = preg_replace("/[\s]+/ui", '-', $res);
		$res = strtolower(preg_replace("/[^0-9a-zа-я\-]+/ui", '', $res));
	    
	    return $res;  
	}
}