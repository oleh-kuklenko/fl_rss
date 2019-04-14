<?php
function requireClass($className, &$classes) {
	if (file_exists(DIR_CLASS . $className . '.class.php')) {
		require_once DIR_CLASS . $className . '.class.php';
	}

	$classes[] = $className;
}

function isValid($classes) {
	foreach ($classes as $class) {
		if (!class_exists($class)) {
			return false;
		}
	}

	return true;
}

// Autoloader
function autoload($class) {
	$file = DIR_LIBRARY . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once $file;

		return true;
	}

	return false;
}

spl_autoload_register('autoload');
spl_autoload_extensions('.php');