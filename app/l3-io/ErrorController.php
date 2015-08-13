<?php

/**
 *  ERRORCONTROLLER.PHP
 *  Error page to display pages not recognised or authorised to display
 *  @author Jonathan Lamb
 */
class ErrorController {

  // instance variables
  private $_AppModel;

  /**
   *  Constructor
   *  Initialise App Model only
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to display the relevant error page
   */
  public function loadFrame() {

    $this->_AppModel->renderFrame("Error");
  }

  /**
   *  LOAD PAGE FRAME WITH 403 MESSAGE
   *  Load the HTML required to display a 403 forbidden page
   */
  public function forbidden() {

    $resources["forbidden"] = true;
    $this->_AppModel->renderFrame("Error", $resources);
  }
}
