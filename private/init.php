<?php
require_once("db.php");

// Autoload
spl_autoload_register('autoloader');

/**
 * Autoloader
 * @param string $className The name of the class
 * @return void
 */
function autoloader($className) {
	require dirname(dirname(__FILE__)) . '/private/classes/' . $className . '.class.php';
}

// Start Session
$session = new Session();
// $message = $session->message();

$errors = [];