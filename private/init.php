<?php
require_once("db.php");

// Environment (production/local)
define("ENV", "local");

// Document root
$root = ($_SERVER["HTTP_HOST"] !== 'assurancex.local') ? '/assurancex/public' : '';
define("ROOT", $root);

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