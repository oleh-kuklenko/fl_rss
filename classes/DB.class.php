<?php
class DB extends Base {
	private $db;

	protected $countAdded = 0;

	public function __construct($driver, $hostname, $username, $password, $database, $port = NULL) {
		$class = 'DB\\' . $driver;

		if(!class_exists($class) && file_exists(DIR_LIBRARY . 'db/' . $driver . '.php')) {
			require(DIR_LIBRARY . 'db/' . $driver . '.php');
		}

		if (class_exists($class)) {
			$this->db = new $class($hostname, $username, $password, $database, $port);
		} else {
			exit('Error: Could not load database driver ' . $driver . '!');
		}
	}

	/**
		Проверяет, существует ли задача
		
		@transliterated - заголовок в транслите
	*/
	public function isExists($transliterated) {
		return $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "tasks WHERE transliterated = '" . $this->db->escape($transliterated) . "'")->row['total'] > 0;
	}

	/**
		Добавляет задачу

		@data - массив данных
		@transliterated - заголовок в транслите
	*/
	public function addTask($data, $transliterated) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "tasks SET title = '" . $this->db->escape($data['title']) . "', description = '" . $this->db->escape($data['description']) . "', transliterated = '" . $this->db->escape($transliterated) . "', link = '" . $this->db->escape($data['link']) . "', category = '" . $this->db->escape($data['category']) . "', date = '" . $this->db->escape($data['pubDate']) . "'");
	}

	/**
		Добавляет задачи

		@tasks - массив задач
	*/
	public function addTasks($tasks) {
		foreach ($tasks as $task) {
			$transliterated = $this->translit($task['title'] . $task['pubDate']);

			if(!$this->isExists($transliterated)) {
				$this->addTask($task, $transliterated);

				++$this->countAdded;
			}
		}

		$this->logging(__CLASS__ . ' | total tasks added: ' . $this->countAdded);
	}

	/**
		Возвращает список не отправленных задач
	*/
	public function getNotSentTasks() {
		return $this->db->query("SELECT * FROM " . DB_PREFIX . "tasks WHERE sended = 0 ORDER BY date ASC")->rows;
	}

	/**
		Определяет задачу как "отправленная"

		@task_id - ID задачи
	*/
	public function setSendingMode($task_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "tasks SET sended = 1 WHERE task_id = '" . (int)$task_id . "'");
	}

	/**
		Проверяет, установлены ли все нужные таблицы

		@tables - массив таблиц
	*/
	public function validateInstall($tables) {
		foreach ($tables as $table) {
			if (!$this->db->query("SHOW TABLES FROM `" . DB_DATABASE . "` LIKE '" . DB_PREFIX . $table . "'")->num_rows) {
				return false;
			}
		}

		return true;
	}

	/**
		Запросы на создание необходимых таблиц

		@tables - массив таблиц
	*/
	public function install() {
		// Список задач
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "tasks` (
				`task_id` INT NOT NULL AUTO_INCREMENT,
				`title` VARCHAR(255) NOT NULL,
				`description` text NOT NULL,
				`transliterated` VARCHAR(255) NOT NULL,
				`link` VARCHAR(255) NOT NULL,
				`category` VARCHAR(255) NOT NULL,
				`sended` int NOT NULL DEFAULT '0',
				`date` datetime NOT NULL,

				PRIMARY KEY(`task_id`)
			) ENGINE=INNODB;
		");
	}

	/**
		Запросы на удаление установленных таблиц из массива таблиц

		@tables - массив таблиц
	*/
	public function uninstall($tables) {
		foreach ($tables as $table) {
			$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . $table . "`");
		}
	}
}
