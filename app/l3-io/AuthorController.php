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
    $_AuthorModel,
    $_questionTypes;

  /**
   *  Constructor
   *  Initialise App, User and Authoring Models; set recognised question types
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();
    $this->_AuthorModel = new AuthorModel();
    $this->_questionTypes = $this->_AppModel->getSchemaList();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the authoring platform in the browser
   */
  public function loadFrame() {

    // get the available question schema types the user may select
    $resources["questionTypes"] = $this->_questionTypes;

    $this->_AppModel->renderFrame("Author", $resources);
  }

  /**
   *  AJAX: GET HTML TEMPLATE FOR A QUESTION
   *  Returns the HTML template of a question
   */
  public function getQuestionTemplate() {

    $template = $this->_AppModel->getPOSTData("qt");
    if (!in_array($template, $this->_questionTypes)) {
      echo "<p>The template for the requested question type does not exist.<br>" .
        "Please contact the system administrator</p>";
      return;
    }

    echo file_get_contents(URL . "app/l4-ui/Author/" . ucfirst($template) . ".html");
  }
}
