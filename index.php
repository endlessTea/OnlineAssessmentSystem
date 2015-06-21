<?php

// define the path to the application folder
define('APP', 'application/');

// load initialisation file
require APP . 'core/init.php';

// start the application
$app = new Application();
