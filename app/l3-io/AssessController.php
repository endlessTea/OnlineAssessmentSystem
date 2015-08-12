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

    // fetch the id's of any tests that the user is enrolled on
    $resources["tests"] = $this->_AssessModel->getListOfAvailableTests(
      $this->_UserModel->getUserData()->userId
    );

    $this->_AppModel->renderFrame("Assess", $resources);
  }

  /**
   *  AJAX: RESPONSE TO REQUEST TO LOAD TEST
   *  Check if a user is able to take a test, return test data if request is valid
   */
  public function loadTest() {

    // attempt to convert test identifier to MongoId
    try {

      $testIdObj = new MongoId($this->_AppModel->getPOSTData("tId"));

    } catch (Exception $e) {

      echo "Invalid test identifier";
      exit;
    }

    // check if the user is eligible to take the test
    $check = $this->_AssessModel->checkTestAvailability(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($check !== true) {
      echo $check;
      exit;
    }

    // load test (Assess Model instance variable will be updated)
    $check = $this->_AssessModel->loadTest(
      $testIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($check !== true) {
      echo $check;
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

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    echo $this->_AssessModel->startTestGetJSONData();
  }
}
