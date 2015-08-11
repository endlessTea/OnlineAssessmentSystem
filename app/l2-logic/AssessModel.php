<?php

/**
 *  ASSESSMODEL.PHP
 *  Sets up tests to be taken, processes student answers and understanding of questions
 *  Issues feedback for incorrect answers and processes students' undersanding of feedback
 *  @author Jonathan Lamb
 */
class AssessModel {

  // store DB utility as instance variable
  private $_DB,
    $_QuestionSchema,
    $_testDocument,
    $_studentId,
    $_expectingAnswers,
    $_expectingStudentFeedback,
    $_questionsFull,
    $_questionsJSON,
    $_feedbackJSON;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations
    $this->_DB = DB::getInstance();

    // import schema
    $this->_QuestionSchema = new QuestionSchema();

    // do not anticipate answers unless set by defined methods
    $this->_expectingAnswers = false;
  }

  /**
   *  GET A LIST OF AVAILABLE TESTS
   *  Check if any tests have been made available to a user by comparing the string rep. of their ID
   *  @return PHP array of data on success, otherwise return specific string
   */
  public function getListOfAvailableTests($studentIdStr) {

    // check userId contains hexadecimal characters only
    if (preg_match('/^([a-z0-9])+$/', $studentIdStr) === 1) {

      // fetch all tests
      $tests = $this->_DB->read("tests", "ALL DOCUMENTS");
      if (!empty($tests)) {

        $availableTests = array();
        foreach ($tests as $tId => $details) {

          if (!isset($details["available"])) continue;
          if (in_array($studentIdStr, $details["available"]))
            $availableTests[] = $tId;
        }

        if (!empty($availableTests)) {

          return $availableTests;
        }
      }
    }

    return "There are no tests available for you to take right now. Please try again later.";
  }

  /**
   *  CHECK TEST AVAILABILITY
   *  Check if a user is eligible to take a test (expects Mongo Id and String params)
   *  @return true (boolean) on success, else false
   */
  public function checkTestAvailability($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      $test = array_pop($test);

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
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      $this->_testDocument = array_pop($test);

      // store student id for reference, initialise test start variable and
      $this->_studentId = $studentIdStr;
      $this->_testStarted = false;
      $this->_questionsFull = array();

      foreach ($this->_testDocument["questions"] as $questionId) {

        // get the corresponding document from MongoDB and add to 'full questions' array
        $document = $this->_DB->read("questions", array("_id" => new MongoId($questionId)));
        $document = array_pop($document);
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
   *  START TEST / GET QUESTIONS
   *  Change the boolean flag to indicate that the test has been started, or check if the test has already started
   *  @return JSON of test data (questions only), else return false to indicate test was already stated
   *  @throws Exception if test has not been initialised as an instance variable
   */
  public function startTestGetJSONData() {

    if (isset($this->_testDocument)) {

      // stop operation if the test has been started already, otherwise change the start indicator
      if ($this->_expectingAnswers) return false;
      $this->_expectingAnswers = true;

      return $this->_questionsJSON;
    }

    throw new Exception("Test has not been initialised");
  }

  /**
   *  PROCESS QUESTION ANSWERS
   *  Update answers to questions if the user's input is valid, test has been started and answers are expected
   *  @return true (boolean) on success, else false
   */
  public function updateTestAnswers($answers) {

    // check if answers are expect and if variable is valid object that can be processed
    if ($this->_expectingAnswers) {
      if (is_object($answers)) {

        // keep track of the number of correctly answered questions and add feedback to root object
        $totalCorrect = 0;
        $feedbackToStudent = new stdClass();

        foreach ($this->_questionsFull as $qNo => $fullQuestion) {

          // fail the operation if the question doesn't exist
          if (!isset($answers->{$qNo})) return false;

          // check if an 'understanding of question' was provided and if the values are valid
          if (!isset($answers->{$qNo}->{'uq'})) return false;
          if ($answers->{$qNo}->{'uq'} !== 0 && $answers->{$qNo}->{'uq'} !== 1) return false;

          // check if an answer was provided at all
          if (!isset($answers->{$qNo}->{'ans'})) return false;

          // Check value of and mark answer
          switch ($fullQuestion["schema"]) {

            case "boolean":

              // answer must be 'TRUE' or 'FALSE' only; mark according to $fullQuestion's 'singleAnswer'
              if ($answers->{$qNo}->{'ans'} !== 'TRUE' && $answers->{$qNo}->{'ans'} !== 'FALSE') return false;
              if ($answers->{$qNo}->{'ans'} === $fullQuestion["singleAnswer"]) {

                $correct = 1;
                $totalCorrect++;

              } else {

                $correct = 0;
                if (isset($fullQuestion["feedback"])) {
                  $feedbackToStudent->{$qNo} = $fullQuestion["feedback"];
                }
              }

              $convertedResponse = array(
                "uq" => $answers->{$qNo}->{'uq'},
                "ca" => $correct
              );
              break;

            default:
              throw new Exception("The question schema '{$fullQuestion["schema"]}' has not been implemented");
          }

          // copy and update the question's "taken" array if it exists, otherwise create a new one to insert
          if (isset($fullQuestion["taken"])) {

            $takenQuestionArray = $fullQuestion["taken"];
            $takenQuestionArray[$this->_studentId] = $convertedResponse;

          } else {

            $takenQuestionArray = array($this->_studentId => $convertedResponse);
          }

          // Update Question: if the operation fails for any question, throw an Exception
          if (!$this->_DB->update("questions", array("_id" => $fullQuestion["_id"]), array("taken" => $takenQuestionArray)))
            throw new Exception("The following question update failed: " . implode($takenQuestionArray));
        }

        // copy and update the tests's "taken" array if it exists, otherwise create a new one to insert
        if (isset($this->_testDocument["taken"])) {

          $takenTestArray = $this->_testDocument["taken"];
          $takenTestArray[$this->_studentId] = $totalCorrect;

        } else {

          $takenTestArray = array($this->_studentId => $totalCorrect);
        }

        // Update Test: if the operation fails for any question, throw an Exception
        if (!$this->_DB->update("tests", array("_id" => $this->_testDocument["_id"]), array("taken" => $takenTestArray)))
          throw new Exception("The following test update failed: " . implode($takenTestArray));

        // remove student from 'available array'
        // http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
        $availableTestArray = $this->_testDocument["available"];
        $key = array_search($this->_studentId, $availableTestArray);
        unset($availableTestArray[$key]);

        // Update 'available' array in Test: if the operation fails for any question, throw an Exception
        if (!$this->_DB->update("tests", array("_id" => $this->_testDocument["_id"]), array("available" => $availableTestArray)))
          throw new Exception("The following test update failed: " . implode($availableTestArray));

        // initialise instance variable of feedback for delivery
        $this->_feedbackJSON = json_encode($feedbackToStudent);

        // update to not expect any further answers to process
        $this->_expectingAnswers = false;

        return true;
      }
    }

    return false;
  }

  /**
   *  GET FEEDBACK FOR INCORRECT ANSWERS - TODO consider adding constraints...?
   *  @return JSON of answer feedback
   *  @throws Exception if answer feedback has not been initialised as an instance variable
   */
  public function issueFeedbackGetJSONData() {

    if (isset($this->_feedbackJSON)){

      return $this->_feedbackJSON;
    }

    throw new Exception("Feedback for student has not been initialised");
  }

  /**
   *  PROCESS FEEDBACK FROM STUDENT
   *  Update user's understanding of feedback if values are valid
   *  @return true (boolean) on success, else false
   */
  public function updateFeedbackFromStudent($studentFeedback) {

    if (is_object($studentFeedback)) {

      // if student feedback was provided for a specific question
      foreach ($this->_questionsFull as $qNo => $fullQuestion) {
        if (isset($studentFeedback->{$qNo})) {

          // fail the operation if an invalid feedback value was provided
          if ($studentFeedback->{$qNo} !== 0 && $studentFeedback->{$qNo} !== 1) return false;

          // obtain the UPDATED version of the question from MongoDB
          $updatedQuestion = array_pop($this->_DB->read("questions", array("_id" => $fullQuestion["_id"])));

          // copy the existing question "taken" array and PUSH the feedback value onto it for the student
          $takenQuestionArray = $updatedQuestion["taken"];
          $takenQuestionArray[$this->_studentId]["uf"] = $studentFeedback->{$qNo};

          // Update Question: if the operation fails for any question, throw an Exception
          if (!$this->_DB->update("questions", array("_id" => $fullQuestion["_id"]), array("taken" => $takenQuestionArray)))
            throw new Exception("The following question update failed: " . implode($takenQuestionArray));
        }
      }

      return true;
    }

    return false;
  }

  // TODO: DELETE ME
  public function _checkInitialised() {

    return $this->_testDocument;
  }
}
