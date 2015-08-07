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

  /**
   *  AJAX: PROCESS NEW QUESTION
   *  Create boolean question - TODO: refactor this method to work with new types
   */
  public function createQuestion() {

    // load question details and return the result of the operation
    $question = array(
      "schema" => "boolean",
      "author" => "f39082hnf3902nf3029",         // $this->_UserModel->getUserData()->userId
      "statement" => $this->_AppModel->getPOSTData("st"),
      "singleAnswer" => $this->_AppModel->getPOSTData("sa"),
      "feedback" => $this->_AppModel->getPOSTData("fb")
    );

    echo ($this->_AuthorModel->createQuestion($question)) ? "<p>Question created!</p>" : "<p>Error creating question</p>";
  }
}
