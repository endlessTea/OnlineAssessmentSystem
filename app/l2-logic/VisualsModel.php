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

  /*        VISUALISATION REQUIREMENTS: Get the model to return JSON data
    // GET STUDENT PERFORMANCE, SINGLE QUESTION
    // GET STUDENT PERFORMANCE, SINGLE TEST
    // GET STUDENT PERFORMANCE, ALL TESTS - IDEA: Scatterplot, single colour
    // GET CLASS PERFORMANCE, SINGLE QUESTION
    // GET CLASS PERFORMANCE, SINGLE TEST - IDEA: Scatterplot, single colour
    // GET CLASS PERFORMANCE, ALL TESTS - IDEA: Scatterplot, multiple colours
    // GET FEEDBACK, SINGLE QUESTION
    // GET FEEDBACK, SINGLE TEST
    // GET FEEDBACK, ALL TESTS

    // http://bl.ocks.org/mbostock/3244058
    // http://bl.ocks.org/mbostock/3183403
    // http://bl.ocks.org/mbostock/3887118
    // http://bl.ocks.org/mbostock/2066421
  */

  /**
   *  GET SINGLE STUDENT PERFORMANCE FOR A SINGLE QUESTION
   *  Get the performance information of a single student for one question, if they have taken it
   *  @return JSON of data on sucess, else false
   */
  public function getStudentPerformanceSingleQuestion($questionIdObj, $studentIdStr) {

    if (is_a($questionIdObj, 'MongoId') && preg_match('/^([a-z0-9])+$/', $studentIdStr) === 1) {

      // get the question from MongoDB and check if the user has taken it
      $question = array_pop($this->_DB->read("questions", array("_id" => $questionIdObj)));
      if (!isset($question["taken"])) return false;
      if (array_key_exists($studentIdStr, $question["taken"])) {

        // return json object with user id and whether or not they correctly answered the question
        $response = new stdClass();
        $response->{$studentIdStr} = $question["taken"][$studentIdStr]["ca"];
        return json_encode($response);
      }
    }

    return false;
  }
}
