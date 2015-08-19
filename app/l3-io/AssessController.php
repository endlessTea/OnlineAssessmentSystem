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

    // check that the current user has an assessor account
    if ($this->_UserModel->getUserData()->accountType !== "student") {
      throw new Exception("User does not have a student account");
    }

    $this->_AssessModel = new AssessModel();
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the assessment platform in the browser
   */
  public function loadFrame() {

    // fetch the id's of any tests that the user is enrolled on
    $resources["tests"] = $this->_AssessModel->getListOfAvailableTests(
      $this->_UserModel->getUserData()->userId
    );

    $this->_AppModel->renderFrame("Assess", $resources);
  }

  /**
   *  GET TEST DISCLAIMER (CHECK USER ALLOWED TO TAKE TEST SPECIFIED)
   *  Return an HTML template to the user for the test disclaimer
   */
  public function checkAndLoadDisclaimer() {

    // attempt to convert test identifier to MongoId
    try {
      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
    } catch (Exception $e) {
      echo "Invalid test identifier";
      exit;
    }

    // check if the user is eligible to take the test
    $check = $this->_AssessModel->checkTestAvailable(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($check !== true) {
      echo "The specified test is not available";
      exit;
    }

    // load the disclaimer
    echo file_get_contents(URL . "app/l4-ui/Assess/Disclaimer.html");
  }

  /**
   *  AJAX: HANDLE REQUEST TO START TEST
   *  Check if JSON data has been loaded (test properly loaded), return JSON if okay
   */
  public function startTest() {

    // attempt to convert test identifier to MongoId
    try {
      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
    } catch (Exception $e) {
      echo "Invalid test identifier.";
      exit;
    }

    // check if the user is eligible to take the test
    $check = $this->_AssessModel->checkTestAvailable(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($check !== true) {
      echo "The specified test is not available.";
      exit;
    }

    // get data, check if json returned (not false (boolean))
    $data = $this->_AssessModel->getQuestionsJSON($testIdObj);
    if ($data === false) {
      echo "There was an issue loading the test.";
      exit;
    }

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo $data;
  }

  /**
   *  AJAX: GET ANSERS FOR SELF-ASSESSMENT QUESTIONS
   *  Return JSON of answers for 'show answer' schema questions
   */
  public function getSelfMarkingAnswers() {

    // attempt to convert test identifier to MongoId
    try {
      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
    } catch (Exception $e) {
      echo "Invalid test identifier.";
      exit;
    }

    // pass data to model, store answers or result in variable
    $data = $this->_AssessModel->getAnswersForSelfMarking(
      $testIdObj
    );
    if ($data === false) {
      echo "There was an issue in retrieving answers for self-marking.";
      exit;
    }

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo $data;
  }

  /**
   *  AJAX: HANDLE ANSWERS SUBMITTED FOR A TEST
   *  Process user's answers to a test, return feedback on success
   */
  public function submitAnswers() {

    // attempt to convert test identifier to MongoId
    try {
      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
    } catch (Exception $e) {
      echo "Invalid test identifier.";
      exit;
    }

    // pass data to model, store feedback or result in variable
    $data = $this->_AssessModel->updateAnswers(
      $testIdObj,
      $this->_UserModel->getUserData()->userId,
      $this->_AppModel->getPOSTData("ans", "getJSON")
    );
    if ($data === false) {
      echo "There was an issue processing your answers.";
      exit;
    }

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo $data;
  }

  /**
   *  AJAX: HANDLE FEEDBACK SUBMITTED BY STUDENT
   *  Process user's feedback in response to incorrectly answered questions
   */
  public function submitFeedback() {

    // attempt to convert test identifier to MongoId
    try {
      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));
    } catch (Exception $e) {
      echo "Invalid test identifier.";
      exit;
    }

    // pass data to model, store feedback or result in variable
    $data = $this->_AssessModel->updateFeedback(
      $testIdObj,
      $this->_UserModel->getUserData()->userId,
      $this->_AppModel->getPOSTData("feed", "getJSON")
    );
    if ($data === false) {
      echo "There was an issue processing your feedback.";
      exit;
    }

    echo "ok";
  }
}
