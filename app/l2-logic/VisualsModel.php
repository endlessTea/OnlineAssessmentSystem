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

  /*      DATA VISUALISATIONS (potential)
    // http://bl.ocks.org/mbostock/3244058
    // http://bl.ocks.org/mbostock/3183403
    // http://bl.ocks.org/mbostock/3887118
    // http://bl.ocks.org/mbostock/2066421
  */

  /**
   *  INTERNAL METHOD: CHECK IF AN OBJECT HAS NO CHILDREN
   *  http://stackoverflow.com/questions/9412126/how-to-check-that-an-object-is-empty-in-php
   *  @return true (boolean) if an object has no children, otherwise false
   */
  private function objectHasNoChildren($object) {

    foreach($object as $child) {
      return false;
    }
    return true;
  }

  /**
   *  GET SINGLE STUDENT PERFORMANCE FOR A SINGLE QUESTION
   *  Get the performance information of a single student for one question, if they have taken it
   *  @return JSON of data on sucess, else false
   */
  public function getStudentPerformanceSingleQuestion($questionIdObj, $studentIdStr) {

    if (is_a($questionIdObj, 'MongoId') && preg_match('/^([a-z0-9])+$/', $studentIdStr) === 1) {

      // get the question from MongoDB and check if the user has taken it
      $question = array_pop($this->_DB->read("questions", array("_id" => $questionIdObj)));
      if (empty($question) || !isset($question["taken"])) return false;
      if (array_key_exists($studentIdStr, $question["taken"])) {

        // return json object with user id and whether or not they correctly answered the question
        $response = new stdClass();
        $response->{$studentIdStr} = $question["taken"][$studentIdStr]["ca"];
        return json_encode($response);
      }
    }

    return false;
  }

  /**
   *  GET SINGLE STUDENT PERFORMANCE FOR A SINGLE TEST
   *  Get the performance of a student for a single test, if they have taken it
   *  @return JSON of data on sucess, else false
   */
  public function getStudentPerformanceSingleTest($testIdObj, $studentIdStr) {

    if (is_a($testIdObj, 'MongoId') && preg_match('/^([a-z0-9])+$/', $studentIdStr) === 1) {

      // get the question from MongoDB and check if the user has taken it
      $test = array_pop($this->_DB->read("tests", array("_id" => $testIdObj)));
      if (empty($test) || !isset($test["taken"])) return false;
      if (array_key_exists($studentIdStr, $test["taken"])) {

        // return json object with user id and whether or not they correctly answered the question
        $response = new stdClass();
        $response->{$studentIdStr} = $test["taken"][$studentIdStr];
        return json_encode($response);
      }
    }

    return false;
  }

  /**
   *  GET SINGLE STUDENT PERFORMANCE FOR ALL TESTS
   *  Get the performance of a student for all tests they have taken
   *  @return JSON of data on sucess, else false if the student has taken no tests
   */
  public function getStudentPerformanceAllTests($studentIdStr) {

    if (preg_match('/^([a-z0-9])+$/', $studentIdStr) === 1) {

      $response = new stdClass();
      $tests = $this->_DB->read("tests", "ALL DOCUMENTS");
      if (empty($tests)) return false;
      foreach ($tests as $tId => $test) {

        if (isset($test["taken"])) {
          if (array_key_exists($studentIdStr, $test["taken"])) {

            $response->{$tId} = $test["taken"][$studentIdStr];
          }
        }
      }

      // if the response does not have children, the user has not taken any tests
      if ($this->objectHasNoChildren($response)) return false;
      return json_encode($response);
    }

    return false;
  }

  /**
   *  GET CLASS PERFORMANCE FOR A SINGLE QUESTION
   *  Get the performance of the class (all students) for a single question
   *  @return JSON of data on sucess, else false if the question hasn't been taken
   */
  public function getClassPerformanceSingleQuestion($questionIdObj) {

    if (is_a($questionIdObj, 'MongoId')) {

      // get the question from MongoDB and check if it exists and anyone has taken it
      $question = array_pop($this->_DB->read("questions", array("_id" => $questionIdObj)));
      if (empty($question) || !isset($question["taken"])) return false;

      // append each user's performance to a root object
      $response = new stdClass();
      foreach ($question["taken"] as $userId => $details) {

        $response->{$userId} = $details["ca"];
      }
      return json_encode($response);
    }

    return false;
  }

  /**
   *  GET CLASS PERFORMANCE FOR A SINGLE TEST
   *  Get the performance of the class (all students) for a single test
   *  @return JSON of data on sucess, else false if the test hasn't been taken
   */
  public function getClassPerformanceSingleTest($testIdObj) {

    if (is_a($testIdObj, 'MongoId')) {

      // get the test from MongoDB and check if it exists and anyone has taken it
      $test = array_pop($this->_DB->read("tests", array("_id" => $testIdObj)));
      if (empty($test) || !isset($test["taken"])) return false;

      // append each user's performance to a root object
      $response = new stdClass();
      foreach ($test["taken"] as $userId => $qCorrect) {

        $response->{$userId} = $qCorrect;
      }
      return json_encode($response);
    }

    return false;
  }

  /**
   *  GET CLASS PERFORMANCE FOR A ALL TESTS
   *  Get the performance of the class (all students) for all tests
   *  @return JSON of data on sucess, else false if there is no data available
   */
  public function getClassPerformanceAllTests() {

    // prepare root response object, request all test documents, check if tests exist at all
    $response = new stdClass();
    $tests = $this->_DB->read("tests", "ALL DOCUMENTS");
    if (empty($tests)) return false;

    // copy the id's and respective number of correct answers to root response object
    foreach ($tests as $tId => $test) {
      if (isset($test["taken"])) {

        $response->{$tId} = $test["taken"];
      }
    }

    // if the response does not have children, there is no available data
    if ($this->objectHasNoChildren($response)) return false;
    return json_encode($response);
  }

  /**
   *  TODO: feedback methods
   *
   */
  /**
   *  TODO: feedback methods
   *
   */
}
