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
    $_QuestionSchema;

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
   *  CHECK IF TEST IS AVAILABLE TO USER
   *  Check if a user is eligible to take a test (expects Mongo Id and String params)
   *  @return true (boolean) if test is available, else false
   */
  public function checkTestAvailable($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test, return false if test doesn't exist
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      if (empty($test)) return false;
      $test = array_pop($test);

      // check if the test is available to the student
      if (isset($test["available"]))
        if (in_array($studentIdStr, $test["available"], true)) return true;
    }

    return false;
  }

  /**
   *  CHECK IF TEST HAS BEEN TAKEN BY A USER
   *  Check if a user has taken a test (expects Mongo Id and String params)
   *  @return true (boolean) if test has been taken, else false
   */
  public function checkTestTaken($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test, return false if test doesn't exist
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      if (empty($test)) return false;
      $test = array_pop($test);

      // check if the test has already been taken by the student
      if (isset($test["taken"]))
        if (array_key_exists($studentIdStr, $test["taken"])) return true;
    }

    return false;
  }

  /**
   *  GET JSON OF QUESTIONS
   *  Start the test by returning JSON representation of questions
   *  @return JSON of data on success, else false on failure
   */
  public function getQuestionsJSON($testIdObj) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the specified test, return false if test doesn't exist
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      if (empty($test)) return false;
      $test = array_pop($test);

      // loop through question Ids and add the corresponding question to an array
      $questions = array();
      foreach ($test["questions"] as $questionId) {

        $document = $this->_DB->read("questions", array("_id" => new MongoId($questionId)));
        $document = array_pop($document);
        $questions[] = $document;
      }

      // convert appropriate values of questions to JSON
      $questionRoot = new stdClass();
      $questionNo = 0;

      foreach($questions as $q) {

        $questionRoot->{$questionNo} = new stdClass();
        $questionRoot->{$questionNo}->schema = $q["schema"];

        // create additional object properties based on recognised schemas
        switch ($q["schema"]) {

          case "boolean":
            $questionRoot->{$questionNo}->question = $q["question"];
            break;

          case "multiple":
            $questionRoot->{$questionNo}->question = $q["question"];
            $questionRoot->{$questionNo}->options = $q["options"];
            break;

          default:
            return false;
        }

        // increment question number
        $questionNo++;
      }

      // return json encoded, reduced question set
      return json_encode($questionRoot);
    }

    return false;
  }

  /**
   *  UPDATE ANSWERS TO QUESTIONS
   *  Expects JSON to parse and validate
   *  @return JSON of feeback on success, else false (boolean)
   */
  public function updateAnswers($testIdObj, $studentIdStr, $answers) {

    // fail the operation if the user is not eligible to take the test
    if (!$this->checkTestAvailable($testIdObj, $studentIdStr)) return false;

    // check if answers are a valid object
    if (is_object($answers)) {

      // get the test and questions from MongoDB
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      $test = array_pop($test);
      $questions = array();
      foreach ($test["questions"] as $questionId) {

        $document = $this->_DB->read("questions", array("_id" => new MongoId($questionId)));
        $document = array_pop($document);
        $questions[] = $document;
      }

      // add score and feedback to response root object
      $response = new stdClass();
      $response->{'score'} = 0;
      $response->{'feedback'} = new stdClass();

      foreach ($questions as $qNo => $fullQuestion) {

        // fail the operation if the question doesn't exist
        if (!isset($answers->{$qNo})) return false;

        // check if an 'understanding of question' was provided and if the values are valid
        if (!isset($answers->{$qNo}->{'uq'})) return false;

        if ($answers->{$qNo}->{'uq'} != 0 && $answers->{$qNo}->{'uq'} != 1) return false;

        // check if an answer was provided at all
        if (!isset($answers->{$qNo}->{'ans'})) return false;

        // Check value of and mark answer
        switch ($fullQuestion["schema"]) {

          case "boolean":

            // answer must be 'TRUE' or 'FALSE' only; mark according to $fullQuestion's 'singleAnswer'
            if ($answers->{$qNo}->{'ans'} !== 'TRUE' && $answers->{$qNo}->{'ans'} !== 'FALSE') return false;
            if ($answers->{$qNo}->{'ans'} === $fullQuestion["singleAnswer"]) {

              $correct = 1;
              $response->{'score'}++;

            } else {

              $correct = 0;
              if (isset($fullQuestion["feedback"])) {
                $response->{'feedback'}->{$qNo} = $fullQuestion["feedback"];
              }
            }

            $convertedResponse = array(
              "uq" => $answers->{$qNo}->{'uq'},
              "ca" => $correct
            );
            break;

          case "multiple":

            // answer must be an array
            if (!is_array($answers->{$qNo}->{'ans'})) return false;

            // determine maximum option size, copy correct answers, initialise wrong answer flag
            $maxOption = count($fullQuestion["options"]) - 1;
            $correctAnswers = $fullQuestion["correctAnswers"];
            $wrongAnswer = false;

            foreach ($answers->{$qNo}->{'ans'} as $answer) {

              // reject any answers that are invalid
              if ($answer < 0 || $answer > $maxOption) return false;

              // for each correct answer in response, remove it from the copied array of correct answers
              if (in_array($answer, $correctAnswers)) {

                // unset by value: identify key and unset key
                // http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
                if (($key = array_search($answer, $correctAnswers)) !== false) {
                  unset($correctAnswers[$key]);
                }

              } else {

                $wrongAnswer = true;
                break;
              }
            }

            // if none of the answers were incorrect and all have been guessed
            if (!$wrongAnswer && empty($correctAnswers)) {

              $correct = 1;
              $response->{'score'}++;

            } else {

              $correct = 0;
              if (isset($fullQuestion["feedback"])) {
                $response->{'feedback'}->{$qNo} = $fullQuestion["feedback"];
              }
            }

            $convertedResponse = array(
              "uq" => $answers->{$qNo}->{'uq'},
              "ca" => $correct
            );
            break;

          default:
            // this should never execute: left in just in case of internal document error
            throw new Exception("The question schema '{$fullQuestion["schema"]}' has not been implemented");
        }

        // copy and update the question's "taken" array if it exists, otherwise create a new one to insert
        if (isset($fullQuestion["taken"])) {

          $takenQuestionArray = $fullQuestion["taken"];
          $takenQuestionArray[$studentIdStr] = $convertedResponse;

        } else {

          $takenQuestionArray = array($studentIdStr => $convertedResponse);
        }

        // Update Question: if the operation fails for any question, throw an Exception
        if (!$this->_DB->update("questions", array("_id" => $fullQuestion["_id"]), array("taken" => $takenQuestionArray)))
          throw new Exception("The following question update failed: " . implode($takenQuestionArray));
      }

      // copy and update the tests's "taken" array if it exists, otherwise create a new one to insert
      if (isset($test["taken"])) {

        $takenTestArray = $test["taken"];
        $takenTestArray[$studentIdStr] = $response->{'score'};

      } else {

        $takenTestArray = array($studentIdStr => $response->{'score'});
      }

      // Update Test: if the operation fails for any question, throw an Exception
      if (!$this->_DB->update("tests", array("_id" => $test["_id"]), array("taken" => $takenTestArray)))
        throw new Exception("The following test update failed: " . implode($takenTestArray));

      // remove student from 'available array'
      // http://stackoverflow.com/questions/7225070/php-array-delete-by-value-not-key
      $availableTestArray = $test["available"];
      $key = array_search($studentIdStr, $availableTestArray);
      unset($availableTestArray[$key]);

      // Update 'available' array in Test: if the operation fails for any question, throw an Exception
      if (!$this->_DB->update("tests", array("_id" => $test["_id"]), array("available" => $availableTestArray)))
        throw new Exception("The following test update failed: " . implode($availableTestArray));

      return json_encode($response);
    }

    return false;
  }

  /**
   *  UPDATE QUESTIONS WITH FEEDBACK-FROM-STUDENT
   *  Expects JSON to parse and validate
   *  @return true (boolean) on success, else false
   */
  public function updateFeedback($testIdObj, $studentIdStr, $feedback) {

    // fail the operation if the user has not taken the test
    if (!$this->checkTestTaken($testIdObj, $studentIdStr)) return false;

    if (is_object($feedback)) {

      // get the test and questions from MongoDB
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      $test = array_pop($test);
      $questions = array();
      foreach ($test["questions"] as $questionId) {

        $document = $this->_DB->read("questions", array("_id" => new MongoId($questionId)));
        $document = array_pop($document);
        $questions[] = $document;
      }

      // if student feedback was provided for a specific question
      foreach ($questions as $qNo => $fullQuestion) {
        if (isset($feedback->{$qNo})) {

          // fail the operation if an invalid feedback value was provided
          if ($feedback->{$qNo} != 0 && $feedback->{$qNo} != 1) return false;

          // obtain the UPDATED version of the question from MongoDB
          $updatedQuestion = $this->_DB->read("questions", array("_id" => $fullQuestion["_id"]));
          $updatedQuestion = array_pop($updatedQuestion);

          // copy the existing question "taken" array and PUSH the feedback value onto it for the student
          $takenQuestionArray = $updatedQuestion["taken"];
          $takenQuestionArray[$studentIdStr]["uf"] = $feedback->{$qNo};

          // Update Question: if the operation fails for any question, throw an Exception
          if (!$this->_DB->update("questions", array("_id" => $fullQuestion["_id"]), array("taken" => $takenQuestionArray)))
            throw new Exception("The following question update failed: " . implode($takenQuestionArray));
        }
      }

      return true;
    }

    return false;
  }
}
