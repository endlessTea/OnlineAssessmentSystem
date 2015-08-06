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

    // No controller provided: take the user to the index of the Home Controller
    if ($this->_URL === "URL parameter not defined") {

      // check for controller: no controller given ? then load start-page
			require 'app/l3-io/HomeController.php';
			$controller = new HomeController();
			$controller->index();
      return;
    }

    // Controller parameter provided: check if a file exists
    if (file_exists('app/l3-io/' . $this->_URL["controller"] . 'Controller.php')) {

      // Set Controller
      require 'app/l3-io/' . $this->_URL["controller"] . 'Controller.php';
      $controller = ucfirst($this->_URL["controller"]) . 'Controller';
      $controller = new $controller();

      // No Action provided: take the user to the index of the above Controller
      if (empty($this->_URL["action"])) {

        $controller->index();
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

    // Controller or Action not recognised: take the user to the index of the Error Controller
    $this->_AppModel->redirectTo(404);
  }
}
