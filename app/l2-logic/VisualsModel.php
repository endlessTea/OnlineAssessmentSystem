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

      $response->{$qId} = $details["question"];
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

      // return array of details about who has taken this question
      return json_encode($question["taken"]);
    }

    return false;
  }


}
