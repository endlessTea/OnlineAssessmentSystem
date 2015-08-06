<?php

/**
 *  URLHANDLER.PHP
 *  Based on the 'Application' class from MINI PHP
 *  Takes URL string and loads the appropriate Controller (l3-io class)
 *  @author original: Panique; modified: Jonathan Lamb
 *  @link https://github.com/panique/mini
 *  @license http://opensource.org/licenses/MIT MIT License
 */
class URLHandler {

  // instance variable for Application Model and URL details
  private $_AppModel,
    $_UserModel,
    $_URL;

  /**
	 * 	Constructor
	 * 	Analyze the URL elements and calls the according controller/method or the fallback
	 */
  public function __construct() {

    // initialise application and user model
    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();

    // TODO - handle user not logged in

    // get current URL values and process them
    $this->_URL = $this->_AppModel->getURL();
    $this->processURL();
  }

  /**
   *  PROCESS URL
   *  Handle array values stored as class instance variable
   *  Load the appropriate controller, action and pass any parameter values if applicable
   */
  private function processURL() {

    // No controller provided: take the user to the UI for the Dashboard (home)
    if ($this->_URL === "URL parameter not defined") {

			require 'app/l3-io/DashboardController.php';
			$controller = new DashboardController();
			$controller->loadFrame();
      return;
    }

    // Controller parameter provided: check if a file exists
    if (file_exists('app/l3-io/' . $this->_URL["controller"] . 'Controller.php')) {

      // Set Controller and load UI
      require 'app/l3-io/' . $this->_URL["controller"] . 'Controller.php';
      $controller = ucfirst($this->_URL["controller"]) . 'Controller';
      $controller = new $controller();
      $controller->loadFrame();
      return;
    }

    // Controller not recognised: take the user to the "404 not found" page
    $this->_AppModel->redirectTo(404);
  }
}
