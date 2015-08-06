<?php

/**
 *  ASSESSCONTROLLER.PHP
 *  Support users registered to take tests to answer questions, and provide/receive feedback
 *  @author Jonathan Lamb
 */
class AssessController {

  // instance variables
  private $_AppModel,
    $_UserModel,
    $_AssessModel;

  /**
   *  Constructor
   *  Initialise App, User and Assessment Models
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
    $this->_AssessModel = new AssessModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the assessment platform in the browser
   */
  public function loadFrame() {

    $this->_AppModel->renderFrame("Assess");
  }
}
