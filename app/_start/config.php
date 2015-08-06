<?php

/**
 *  CONFIG.PHP
 *  Enable error reporting, define base URL location and global configuration details
 *  Adapted from 'config.php', MINI PHP
 *  @author original: Panique; modified: Jonathan Lamb
 *  @link https://github.com/panique/mini
 *  @license http://opensource.org/licenses/MIT MIT License
 */

// enable error reporting and allow errors to be displayed (remove in production)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Define String of URL base location
define('URL_PUBLIC_FOLDER', 'public');
define('URL_PROTOCOL', 'http://');
define('URL_DOMAIN', $_SERVER['HTTP_HOST']);
define('URL_SUB_FOLDER', str_replace(URL_PUBLIC_FOLDER, '', dirname($_SERVER['SCRIPT_NAME'])));
define('URL', URL_PROTOCOL . URL_DOMAIN . URL_SUB_FOLDER . '/');

// Configuration details (database: production)
$GLOBALS['config'] = array(
  'mongodb' => array(
    'host' => 'localhost',
    'db' => 'msc_v1'
  )
);

// Load all level 1 and 2 components, plus the URL handler
require 'app/l1-utils/DB.php';
require 'app/l1-utils/QuestionSchema.php';
require 'app/l1-utils/SG.php';
require 'app/l1-utils/TestSchema.php';
require 'app/l2-logic/AppModel.php';
require 'app/l2-logic/AssessModel.php';
require 'app/l2-logic/AuthorModel.php';
require 'app/l2-logic/UserModel.php';
require 'app/l2-logic/VisualsModel.php';
require 'app/l3-io/URLHandler.php';
