<?php
error_reporting(E_ALL);

use Phalcon\Mvc\Application;
use Phalcon\Config\Adapter\Php as ConfigPHP;

try {
	define('APP_PATH', realpath('..') . '/app_web/');

	/**
	 * Read the configuration
	 */
	$config = new ConfigPHP(APP_PATH . 'config/config.php');

	/**
	 * Auto-loader configuration
	 */
	require APP_PATH .'/config/loader.php';

	/**
	 * Load application services
	 */
	require APP_PATH .'/config/services.php';
	$application = new Application($di);
	echo $application->handle()->getContent();

} catch (Exception $e){
	echo $e->getMessage();
}
