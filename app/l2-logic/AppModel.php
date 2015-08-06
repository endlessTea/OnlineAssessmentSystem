<?php

/**
 *  APPMODEL.PHP
 *  Central application functions, including template rendering,
 *  redirecting and intermediary functions for (safely) obtaining $_GET/$_POST data
 *  @author Jonathan Lamb
 */
class AppModel {

  private $_testMode;

  /**
   *  Constructor
   *  Allows AppModel to be used in test or production mode
   */
  public function __construct($testMode = null) {

    if ($testMode === "testMode") {

      $this->_testMode = true;

    } else {

      $this->_testMode = false;
    }

    $this->_SG = new SG();
  }

  /**
   *  GET '$_GET' DATA
   *  Get escaped data sent via GET request
   *  @return escaped data from $_GET
   */
  public function getGETData($key) {
    return $this->_SG->get($key, "escape");
  }

  /**
   *  GET URL
   *  Get URL from $_GET superglobal
   *  @return array of values signifying which controller, action and parameters to use
   */
  public function getURL() {
    return $this->_SG->get("url");
  }

  /**
   *  GET '$_POST' DATA
   *  Get escaped data sent via POST request
   *  @return escaped data from $_POST
   */
  public function getPOSTData($key) {
    return $this->_SG->post($key, "escape");
  }

  /**
   *  RENDER FRAME
   *  Render frame based on controller, extracting resources into scope
   *  @return (test mode only) array of string values representing files that would be 'required'
   */
  public function renderFrame($controller, $resources = array()) {

    extract($resources);
    $frame = "app/l4-ui/" . $controller . "/Frame.php";

    // if testmode is on, return the string representation of the frame, otherwise load it
    if ($this->_testMode) return $frame;
    else require $frame;
  }

  /**
   *  REDIRECT
   *  Re-implementation of the 'Redirect' class from PHP Academy's OOP Login System
   *  @author original: PHP Academy; modified: Jonathan Lamb
   *  @link https://github.com/adamaoc/login_reg
   *  @license None
   */
  public function redirectTo($location) {

    // check if error code was passed as an argument
		if(is_numeric($location)) {

      // render template based on number passed in
			switch($location) {

				case 404:
          $headerString = "Location: " . URL . "error";
					break;

        case 403:
          $headerString = "Location: " . URL . "forbidden";
          break;
			}

    } else {

      // use location reference for "header" string
      $headerString = "Location: " . $location;
    }

    // return string in test mode, otherwise redirect user
    if ($this->_testMode) {

      return $headerString;

    } else {

      header($headerString);
  		exit();
    }
  }
}
