<?php

/**
 *  ASSESSMODEL.PHP
 *
 *  @author Jonathan Lamb
 */
class AssessModel {

  // store DB utility as instance variable
  private $_DB,
    $_QuestionSchema,
    $_testDocument,
    $_studentId,
    $_testStarted,
    $_questionsFull,
    $_questionsJSON;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations
    $this->_DB = DB::getInstance();

    // import schema
    $this->_QuestionSchema = new QuestionSchema();
  }

  /**
   *  CHECK TEST AVAILABILITY
   *  Check if a user is eligible to take a test (expects Mongo Id and String params)
   *  @return true (boolean) on success, else false
   */
  public function checkTestAvailability($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test
      $test = array_pop($this->_DB->read("tests", array("_id" => $testIdObj)));

      // check if the student has already taken the test
      if (isset($test["taken"]))
        if (array_key_exists($studentIdStr, $test["taken"]))
          return "User '{$studentIdStr}' has already taken this test.";

      // check if the test is available to the student
      if (isset($test["available"]))
        if (in_array($studentIdStr, $test["available"], true)) return true;
    }

    return false;
  }

  /**
   *  LOAD TEST
   *  Update instance variable 'testDocument'
   *  @return true (boolean) on success
   *  @throws Exception if test cannot be loaded
   */
  public function loadTest($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test and store as instance variable
      $this->_testDocument = array_pop($this->_DB->read("tests", array("_id" => $testIdObj)));

      // store student id for reference, initialise test start variable and
      $this->_studentId = $studentIdStr;
      $this->_testStarted = false;
      $this->_questionsFull = array();

      foreach ($this->_testDocument["questions"] as $questionId) {

        // get the corresponding document from MongoDB and add to 'full questions' array
        $document = array_pop($this->_DB->read("questions", array("_id" => new MongoId($questionId))));
        $this->_questionsFull[] = $document;
      }

      // covert question set into JSON format for assessment; store as instance variable
      $this->_questionsJSON = $this->convertQuestionsToJSON();

      if ($this->_testDocument != null
        && !empty($this->_questionsFull) 
        && $this->_questionsJSON !== false) return true;
    }

    throw new Exception("Invalid test identifier / MongoId");
  }

  /**
   *  CONVERT QUESTIONS
   *  Convert full questions into presentable JSON format to return to student
   *  @return true (boolean) on success
   *  @throws Exception if questions have not been initialised before attempted conversion
   */
  public function convertQuestionsToJSON() {

    if (isset($this->_questionsFull)) {

      // create a base object and question counter
      $questionRoot = new stdClass();
      $questionNo = 0;

      foreach ($this->_questionsFull as $fullQuestion) {

        $questionRoot->{$questionNo} = new stdClass();
        $questionRoot->{$questionNo}->schema = $fullQuestion["schema"];

        // create additional object properties based on recognised schemas
        switch ($fullQuestion["schema"]) {

          case "boolean":
            $questionRoot->{$questionNo}->statement = $fullQuestion["statement"];
            break;

          default:
            throw new Exception("The question schema '{$fullQuestion["schema"]}' has not been implemented");
        }

        // increment question number
        $questionNo++;
      }

      // return json encoded, reduced question set
      return json_encode($questionRoot);
    }

    throw new Exception("Test questions have not been initialised");
  }

  /**
   *  START TEST
   *  Return an indication of
   *  @return JSON of test data (questions only), else return false to indicate test was already stated
   *  @throws Exception if test has not been initialised as an instance variable
   */
  public function startTestGetJSONData() {

    if (isset($this->_testDocument)) {

      // stop operation if the test has been started already, otherwise change the start indicator
      if ($this->_testStarted) return false;
      $this->_testStarted = true;
      return $this->_questionsJSON;
    }

    throw new Exception("Test has not been initialised");
  }

  /**
   *  UPDATE TEST ANSWERS
   *
   */
  public function updateTestAnswers() {

    // check if the test was already submitted...
  }

}
