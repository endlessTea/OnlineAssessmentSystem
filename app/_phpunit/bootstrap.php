<?php

/**
 *  BOOTSTRAP.PHP
 *  Load all classes in the application folder to enable unit testing
 *  http://jes.st/2011/phpunit-bootstrap-and-autoloading-classes/
 */

// MongoDB connection info, set up test DB
$GLOBALS['config'] = array(
  'mongodb' => array(
    'host' => 'localhost',
    'db' => 'phpunit'
  )
);

// Use AutoLoader script and register all classes in PHP files of app directory
include_once('AutoLoader.php');
AutoLoader::registerDirectory('..' . DIRECTORY_SEPARATOR . 'l1-utils');

// Recursively print all classes loaded by AutoLoader for reference
Autoloader::printClassNames();
