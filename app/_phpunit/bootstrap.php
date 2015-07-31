<?php

/**
 *  BOOTSTRAP.PHP
 *  Load all classes in the application folder to enable unit testing
 *  @author Jess Telford
 *  @link http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 *  @license None
 */

// MongoDB connection info: use a practice database for unit testing
$GLOBALS['config'] = array(
  'mongodb' => array(
    'host' => 'localhost',
    'db' => 'phpunit'
  )
);

// define URL string for testing purposes
define('URL', 'http://localhost/msc/');

// Use AutoLoader script and register all classes in PHP files of app directory
include_once('AutoLoader.php');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'l1-utils');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'l2-logic');

// Recursively print all classes loaded by AutoLoader for reference
Autoloader::printClassNames();
