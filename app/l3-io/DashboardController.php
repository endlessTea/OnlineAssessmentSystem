<?php

/**
 *  DASHBOARDCONTROLLER.PHP
 *  Landing page for users once logged in
 *  Presents visualisation statistics to assessor accounts
 *  @author Jonathan Lamb
 */
class DashboardController {

  // instance variables
  private $_AppModel,
    $_UserModel,
    $_VisualsModel;

  /**
   *  Constructor
   *  Initialise App, User and Visualisation Models
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
    $this->_VisualsModel = new VisualsModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the dashboard in the browser
   */
  public function loadFrame() {

    $resources["fullName"] = $this->_UserModel->getUserData()->fullName;
    $this->_AppModel->renderFrame("Dashboard", $resources);
  }

  /**
   *  LOG USER OUT
   */
  public function logout() {

    $this->_UserModel->logUserOut();
    $this->_AppModel->redirectTo(URL);
  }
}
