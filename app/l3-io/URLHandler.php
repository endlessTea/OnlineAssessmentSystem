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

    // initialise application, user model and set URL instance var.
    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
    $this->_URL = $this->_AppModel->getURL();

    // User is not logged in: redirect to UI of Login controller
    if (!$this->_UserModel->getLoginStatus()) {

      $this->processLogin();

    } else {

      // process URL values as user will be logged in
      $this->processURL();
    }
  }

  /**
   *  PROCESS LOGIN REQUEST
   *  Either load UI for Login Controller, or call login method of the controller
   */
  public function processLogin() {

    require 'app/l3-io/LoginController.php';
    $controller = new LoginController();

    // check if the request was to log a user in
    if (isset($this->_URL["action"])) {
      if ($this->_URL["action"] === "logUserIn") {

        $controller->logUserIn();
        return;

      } elseif ($this->_URL["action"] === "getRegistrationForm") {

        $controller->getRegistrationForm();
        return;

      } elseif ($this->_URL["action"] === "registerNewUser") {

        $controller->registerNewUser();
        return;
      }
    }

    // otherwise direct to landing page
    $controller->loadFrame();
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

      // No Action provided: take the user to the index of the above Controller
      if (empty($this->_URL["action"])) {

        $controller->loadFrame();
        return;
      }

      // Recognised Action provided: check if additional parameters provided
      if (method_exists($controller, $this->_URL["action"])) {

        // Additional parameters: call function and pass parameters as arguments
        if (!empty($this->_URL["parameters"])) {

          call_user_func_array(
            array($controller, $this->_URL["action"]),
            $this->_URL["parameters"]
          );
          return;
        }

        // No additional parameters: call function without arguments
        $controller->{$this->_URL["action"]}();
        return;
      }
    }

    // Controller or Action not recognised: take the user to the UI of the Error Controller
    $this->_AppModel->redirectTo(404);
  }
}
