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
   *  AJAX: GET NEW QUESTION TEMPLATE
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
      "author" => $this->_UserModel->getUserData()->userId,
      "statement" => $this->_AppModel->getPOSTData("st"),
      "singleAnswer" => $this->_AppModel->getPOSTData("sa"),
      "feedback" => $this->_AppModel->getPOSTData("fb")
    );

    echo ($this->_AuthorModel->createQuestion($question)) ? "<p>Question created!</p>" : "<p>Error creating question</p>";
  }

  /**
   *  AJAX: MANAGE QUESTIONS
   *  Returns JSON of user data to manage user questions
   */
  public function getQuestions() {

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo json_encode($this->_AuthorModel->getQuestions(
      $this->_UserModel->getUserData()->userId
    ));
  }

  /**
   *  AJAX: DELETE QUESTION
   *  Request to delete a question; returns an indication of success/failure
   */
  public function deleteQuestion() {

    echo ($this->_AuthorModel->deleteQuestion(
      new MongoId($this->_AppModel->getPOSTData("qId")),
      $this->_UserModel->getUserData()->userId
    )) ? "<p>Question deleted!</p>" : "<p>Error deleting question</p>";
  }

  /**
   *  AJAX: CREATE TEST
   *  Process question Id's and create a new document
   */
  public function createTest() {

    $test = array(
      "schema" => "standard",
      "author" => $this->_UserModel->getUserData()->userId,
      "questions" => $this->_AppModel->getPOSTData("qs", "getJSON")
    );

    echo ($this->_AuthorModel->createTest($test)) ? "<p>Test created!</p>" : "<p>Error creating test</p>";
  }

  /**
   *  AJAX: MANAGE TESTS
   *  Returns JSON of user data to manage user tests
   */
  public function getTests() {

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo json_encode($this->_AuthorModel->getTests(
      $this->_UserModel->getUserData()->userId
    ));
  }

  /**
   *  AJAX: DELETE TEST
   *  Request to delete a test; returns an indication of success/failure
   */
  public function deleteTest() {

    echo ($this->_AuthorModel->deleteTest(
      new MongoId($this->_AppModel->getPOSTData("tId")),
      $this->_UserModel->getUserData()->userId
    )) ? "<p>Test deleted!</p>" : "<p>Error deleting test</p>";
  }

  /**
   *  AJAX: GET STUDENTS FOR TEST
   *  Return a list of students that can take the test, have taken the test or have already been assigned
   */
  public function getStudentsForTest() {

    // check that the user requesting data has a valid assessor account
    if ($this->_UserModel->getUserData()->accountType !== "assessor") {
      print_r($this->_UserModel->getUserData()->accountType);
      echo "Invalid account type to request data";
      exit;
    }

    // attempt to convert test identifier to MongoId
    try {

      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));

    } catch (Exception $e) {

      echo "Invalid test identifier";
      exit;
    }

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    // get students for test
    echo $this->_AuthorModel->getStudentsForTest(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
  }

  /**
   *  AJAX: ISSUE TEST TO ANOTHER USER
   *  Register another user to be eligible to take a test
   */
  public function issueTest() {

    // attempt to convert test and user id identifier to MongoIds
    try {

      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
      $userIdObj = new MongoId($this->_AppModel->getPOSTData("sId"));

    } catch (Exception $e) {

      echo "Invalid test identifier";
      exit;
    }

    echo ($this->_AuthorModel->makeTestAvailableToUser(
      $testIdObj,
      $userIdObj
    )) ? "<p>Test issued!</p>" : "<p>Error issuing test</p>";
  }
}
