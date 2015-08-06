<?php

/**
 *  AUTHORCONTROLLER.PHP
 *  Allow assessors to compose questions and tests, registering users to take the tests
 *  @author Jonathan Lamb
 */
class AuthorController {

  // instance variables
  private $_AppModel,
    $_UserModel,
    $_AuthorModel;

  /**
   *  Constructor
   *  Initialise App, User and Authoring Models
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
    $this->_AuthorModel = new AuthorModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the authoring platform in the browser
   */
  public function loadFrame() {

    $this->_AppModel->renderFrame("Author");
  }
}
