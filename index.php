<?php

/**
 *  INDEX.PHP (site index)
 *  Load the initialisation file
 *  Instantiate the URL handler (redirect to home/index or home/login)
 */

require 'app/_start/config.php';
$url = new URLHandler();
