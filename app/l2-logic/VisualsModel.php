<?php

/**
 *  VISUALSMODEL.PHP
 *  Returns data required for visualisation of performance and question/feedback understanding
 *  @author Jonathan Lamb
 */
class VisualsModel {

  // store DB utility as instance variable
  private $_DB;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations
    $this->_DB = DB::getInstance();
  }

  /**
   *  GET LIST OF QUESTIONS FOR AN ASSESSOR
   *  Get question id's and question names
   *  @return JSON of data on sucess, otherwise false if there is no data to return
   */
  public function getListOfQuestions($assessorIdStr) {

    // reject an assessor id that does not consist of hexadecimal characters
    if (preg_match('/^([a-z0-9])+$/', $assessorIdStr) !== 1) return false;

    // check the author string against the authors for each question
    $questions = $this->_DB->read("questions", array("author" => $assessorIdStr));
    if (empty($questions)) return false;

    // create root object and append id and question statement
    $response = new stdClass();
    foreach ($questions as $qId => $details) {

      $response->{$qId} = $details["name"];
    }

    return json_encode($response);
  }

  /**
   *  GET SINGLE QUESTION DATA (JSON)
   *  Get students understanding of, performance on and understanding of feedback for a single question
   *  @return JSON of data on success, else false if the question doesn't exist / hasn't been taken
   */
  public function getSingleQuestionJSON($questionIdObj, $assessorIdStr) {

    if (is_a($questionIdObj, 'MongoId')) {

      // get the question from MongoDB and check if it exists and anyone has taken it
      $question = $this->_DB->read("questions", array("_id" => $questionIdObj));
      $question = array_pop($question);
      if (empty($question) || !isset($question["taken"]) || $question["author"] !== $assessorIdStr) return false;

      // copy 'taken' array and add full user name with each user id
      $response = $question["taken"];
      foreach ($response as $uId => $details) {

        // identify the user's full name by getting the document from MongoDB
        $user = $this->_DB->read("users", array("_id" => new MongoId($uId)));
        $user = array_pop($user);
        $response[$uId]["name"] = $user["full_name"];
      }

      // return array of details about who has taken this question
      return json_encode($response);
    }

    return false;
  }

  /**
   *  GET LIST OF TESTS FOR AN ASSESSOR
   *  Get test id's and names
   *  @return JSON of data on sucess, otherwise false if there is no data to return
   */
  public function getListOfTests($assessorIdStr) {

    // reject an assessor id that does not consist of hexadecimal characters
    if (preg_match('/^([a-z0-9])+$/', $assessorIdStr) !== 1) return false;

    // check the author string against the authors for each test
    $tests = $this->_DB->read("tests", array("author" => $assessorIdStr));
    if (empty($tests)) return false;

    // create root object and append id and test name
    $response = new stdClass();
    foreach ($tests as $tId => $details) {

      $response->{$tId} = $details["name"];
    }

    return json_encode($response);
  }

  /**
   *  GET SINGLE TEST DATA (JSON)
   *  Get total students understanding of, performance on and understanding of feedback for a single test
   *  @return JSON of data on success, else false if the test doesn't exist / hasn't been taken
   */
  public function getSingleTestJSON($testIdObj, $assessorIdStr) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the test from MongoDB and check if it exists and anyone has taken it
      $test = $this->_DB->read("tests", array("_id" => $testIdObj));
      $test = array_pop($test);
      if (empty($test) || !isset($test["taken"]) || $test["author"] !== $assessorIdStr) return false;

      // copy 'taken' array add general test info and add full user name with each user id
      $response = array(
        "testData" => array(
          "totalQuestions" => count($test["questions"])
        ),
        "userData" => $test["taken"]
      );
      foreach ($response["userData"] as $uId => $details) {

        // identify the user's full name by getting the document from MongoDB
        $user = $this->_DB->read("users", array("_id" => new MongoId($uId)));
        $user = array_pop($user);
        $response["userData"][$uId]["name"] = $user["full_name"];
      }

      // return array of details about who has taken this test
      return json_encode($response);
    }

    return false;
  }
}
