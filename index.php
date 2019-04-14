<?php
// include config
if (file_exists('config.php')) {
	require_once('config.php');
}

// include additional functions
if (file_exists(DIR_APPLICATION . 'functions.php')) {
	require_once(DIR_APPLICATION . 'functions.php');
}

// include composer dependies
if (file_exists(DIR_APPLICATION . 'vendor/autoload.php')) {
	require_once(DIR_APPLICATION . 'vendor/autoload.php');
}

// required classes
$requiredClasses = array();

// require base class
requireClass('Base', $requiredClasses);

// require base class for get tasks
requireClass('ParsingFL', $requiredClasses);

// require data base class
requireClass('DB', $requiredClasses);

// require telegram request class
requireClass('TelegramRequest', $requiredClasses);

// require telegram sender class
requireClass('TelegramSender', $requiredClasses);

if(isValid($requiredClasses)) {
	// init database connect
	$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

	$_tables = array(
		'tasks'	
	);

	if(!$db->validateInstall($_tables)) {
		$db->install($_tables);
	}

	if(isset($_GET['uninstall']) && (int)$_GET['uninstall']) {
		$db->uninstall($_tables);

		die('Uninstallation completed!');
	}
	// init database connect

	// parsing
	$parsing = new ParsingFL();

	$parsing->processing();
	// parsing

	// add to database
	$db->addTasks($parsing->getTasks());
	// add to database

	// send to telegram bot
	$telegramSender = new TelegramSender();

	foreach ($db->getNotSentTasks() as $task) {
		$message = $telegramSender->formatting($task);

		if($telegramSender->send($message)) {
			$db->setSendingMode($task['task_id']);
		}
	}
	// send to telegram bot

	echo 'success';
} else {
	die('Wrong initialize!');
}