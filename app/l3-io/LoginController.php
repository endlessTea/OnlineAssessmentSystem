<?php

/**
 *  LOGINCONTROLLER.PHP
 *  Landing page for users that are not logged in
 *  @author Jonathan Lamb
 */
class LoginController {

  // instance variables
  private $_AppModel,
    $_UserModel;

  /**
   *  Constructor
   *  Initialise App and User Models
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the dashboard in the browser
   */
  public function loadFrame() {

    $this->_AppModel->renderFrame("Login");
  }

  /**
   *  AJAX: HANDLE USER LOGIN REQUEST
   *  If valid credentials were supplied, log the user in (set session)
   *  On success, return indicator that session set, otherwise an indicator of failure
   */
  public function logUserIn() {

    echo ($this->_UserModel->logUserIn(
      $this->_AppModel->getPOSTData("u"),
      $this->_AppModel->getPOSTData("p")
    )) ? "sessionSet" : "invalid";
  }

  /**
   *  AJAX: REGISTER NEW USER
   *  Process username and password, register as new user if no issues with data
   *  On success, return indicator that the new user was registered otherwise an indicator of failure
   */
  public function registerNewUser() {
    
    echo ($this->_UserModel->createUser(
      $this->_AppModel->getPOSTData("u"),
      $this->_AppModel->getPOSTData("p")
    )) ? "userRegistered" : "invalid";
  }
}
