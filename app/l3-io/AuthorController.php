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

    // check that the current user has an assessor account
    if ($this->_UserModel->getUserData()->accountType !== "assessor") {
      throw new Exception("User does not have an assessor account");
    }

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
   *  Create new question based on question type
   */
  public function createQuestion() {

    $questionType = $this->_AppModel->getPOSTData("qt");
    switch ($questionType) {

      case "boolean":

        $question = array(
          "schema" => "boolean",
          "name" => $this->_AppModel->getPOSTData("qn"),
          "author" => $this->_UserModel->getUserData()->userId,
          "question" => $this->_AppModel->getPOSTData("qu"),
          "singleAnswer" => $this->_AppModel->getPOSTData("sa"),
          "feedback" => $this->_AppModel->getPOSTData("fb")
        );

        break;

      case "multiple":

        $question = array(
          "schema" => "multiple",
          "name" => $this->_AppModel->getPOSTData("qn"),
          "author" => $this->_UserModel->getUserData()->userId,
          "question" => $this->_AppModel->getPOSTData("qu"),
          "options" => $this->_AppModel->getPOSTData("op", "getJSON"),
          "correctAnswers" => $this->_AppModel->getPOSTData("ca", "getJSON"),
          "feedback" => $this->_AppModel->getPOSTData("fb")
        );

        break;

      case "pattern":

        $question = array(
          "schema" => "pattern",
          "name" => $this->_AppModel->getPOSTData("qn"),
          "author" => $this->_UserModel->getUserData()->userId,
          "question" => $this->_AppModel->getPOSTData("qu"),
          "pattern" => $this->_AppModel->getPOSTData("rx"),
          "feedback" => $this->_AppModel->getPOSTData("fb")
        );

        break;

      case "short":

        $question = array(
          "schema" => "short",
          "name" => $this->_AppModel->getPOSTData("qn"),
          "author" => $this->_UserModel->getUserData()->userId,
          "question" => $this->_AppModel->getPOSTData("qu"),
          "answer" => $this->_AppModel->getPOSTData("ans"),
          "feedback" => $this->_AppModel->getPOSTData("fb")
        );

        break;

      default:
        echo "<p>Error: unrecognised question type</p>";
        exit;
    }

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
   *  AJAX: GET USERS
   *  Get a list of user IDs and names to create distribution groups
   */
  public function getStudents() {

    header('Content-Type: application/json');
    echo $this->_UserModel->getListOfStudents();
  }

  /**
   *  AJAX: CREATE DISTRIBUTION GROUP
   *  Create a user group based on student ids
   */
  public function createGroup() {

    echo ($this->_UserModel->createGroup(
      $this->_AppModel->getPOSTData("gn"),
      $this->_AppModel->getPOSTData("us", "getJSON")
    )) ? "<p>Group created!</p>" : "<p>Error creating group</p>";
  }

  /**
   *  AJAX: GET GROUPS
   *  Get group details for management page
   */
  public function getGroups() {

    header('Content-Type: application/json');
    echo $this->_UserModel->getListOfGroups();
  }

  /**
   *  AJAX: GET GROUP MEMBER DETAILS
   *  Get group details for management page
   */
  public function getGroupMemberDetails() {

    header('Content-Type: application/json');
    echo $this->_UserModel->getGroupMemberDetails(
      new MongoId($this->_AppModel->getPOSTData("gId"))
    );
  }

  /**
   *  AJAX: DELETE GROUP
   *  Request to delete a group; returns an indication of success/failure
   */
  public function deleteGroup() {

    echo ($this->_UserModel->deleteGroup(
      new MongoId($this->_AppModel->getPOSTData("gId"))
    )) ? "<p>Group deleted!</p>" : "<p>Error deleting group</p>";
  }

  /**
   *  AJAX: CREATE TEST
   *  Process question Id's and create a new document
   */
  public function createTest() {

    $test = array(
      "schema" => "standard",
      "name" => $this->_AppModel->getPOSTData("tn"),
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
  public function getTestDetails() {

    // attempt to convert test identifier to MongoId
    try {

      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));

    } catch (Exception $e) {

      echo "Invalid test identifier";
      exit;
    }

    $data = $this->_AuthorModel->getFullTestDetails(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($data == false) {

      echo "Unable to retrieve test details";

    } else {

      echo $data;
    }
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
      $userOrGroupIdObj = new MongoId($this->_AppModel->getPOSTData("ugId"));

    } catch (Exception $e) {

      echo "Invalid test identifier";
      exit;
    }

    $usage = $this->_AppModel->getPOSTData("u");
    if ($usage === "student") {

      echo ($this->_AuthorModel->makeTestAvailableToUser(
        $testIdObj,
        $userOrGroupIdObj
      )) ? "<p>Test issued!</p>" : "<p>Error issuing test</p>";

    } elseif ($usage === "group") {

      echo ($this->_AuthorModel->makeTestAvailableToGroup(
        $testIdObj,
        $userOrGroupIdObj
      )) ? "<p>Test issued!</p>" : "<p>Error issuing test</p>";

    } else {

      echo "Invalid usage: " . $usage;
    }
  }
}
