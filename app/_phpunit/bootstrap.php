<?php

/**
 *  BOOTSTRAP.PHP
 *  Load all classes in the application folder to enable unit testing
 *  http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 */

// MongoDB connection info, set up test DB
$dbConfig = array(
  'host' => 'localhost',
  'db' => 'phpunit'
);

// Use AutoLoader script and register all directories / PHP files in app folder
include_once('AutoLoader.php');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'models');

// Recursively print all classes loaded by AutoLoader for reference
Autoloader::printClassNames();
