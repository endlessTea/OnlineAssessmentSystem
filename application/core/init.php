<?php 
	
	// enable sessions
	session_start();

	// enable error reporting and allow errors to be displayed
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	// define URL info: public folder, protocol, domain, sub folder and URL path
	define('URL_PUBLIC_FOLDER', 'public');
	define('URL_PROTOCOL', 'http://');
	define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
	define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
	define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER . '/');

	// define database connection info, cookie values and session values
	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => 'localhost',
			'username' => 'root',
			'password' => 'magma2185top',
			'db' => 'msc_v1'
		)
		// login config removed
	);

	// auto-load all helper classes in libs directory
	spl_autoload_register(function($class) {
		require APP . 'libs/' . $class . '.php';
	});
	
	// user session config removed
