<?php

/**
 *  AUTHORMODEL.PHP
 *
 *  @author Jonathan Lamb
 */
class AuthorModel {

  // store DB utility question and test schema as instance variables
  private $_DB,
    $_questionSchema,
    $_testSchema;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations
    $this->_DB = DB::getInstance();

    // define question schema: restrict document structure of questions
    $this->_questionSchema = array(
      'boolean' => array(
        'schema' => 'required',
        'author' => 'required',
        'statement' => 'required',
        'singleAnswer' => 'required',
        'feedbackCorrect' => 'optional',
        'feedbackIncorrect' => 'optional'
      )
    );

    // define test schema: restrict document structure of tests
    $this->_testSchema = array(
      'todo'
    );
  }

  #############################################
  ################# QUESTIONS #################
  #############################################

  /**
   *  CREATE A QUESTION
   *  Create a question if the schema is valid and the question values follow schema requirements
   *  @return true (boolean) on success, else false
   */
  public function createQuestion($schema, $question = array()) {

    // check if schema is recognised
    if (array_key_exists($schema, $this->_questionSchema)) {

      // prepare document to insert (valid data will be transfered to this new variable)
      $document = array();

      // loop through each schema property
      foreach ($this->_questionSchema[$schema] as $sProperty => $sRequirement) {

        // if a required property was not provided, fail the operation
        if ($sRequirement === 'required' && !isset($question[$sProperty])) return false;

        // if property is required or property is optional AND there is a value that can be used
        if ($sRequirement === 'required' ||
         ($sRequirement === 'optional' && isset($question[$sProperty]))) {

          // copy item from the question to the insertion document and remove it from question array
          $document[$sProperty] = $question[$sProperty];
          unset($question[$sProperty]);
        }
      }

      // any remaining data is not part of the schema, fail the operation
      if (!empty($question)) return false;

      // otherwise the question provided was valid, return the result of the DB operation
      return $this->_DB->create('questions', $document);
    }

    return false;
  }

  /**
   *  GET (READ) QUESTIONS
   *  Get all questions matching a user ID
   *  @return document(s) as PHP array (empty if no docs)
   */
  public function getQuestions($userId) {

    // check userId contains hexadecimal characters only (fail if otherwise)
    if (preg_match('/^([a-z0-9])+$/', $userId) === 0) return false;

    // return data
    return $this->_DB->read('questions', array('author' => $userId));
  }

  /**
   *  UPDATE A QUESTION
   *  Update the value of a single question (expects MongoId object)
   *  If key exists in schema and operation is permitted
   *  @return true (boolean) on success, else false
   */
  public function updateQuestion($questionId, $update = array()) {

    // check questionId is MongoId object
    if (is_a($questionId, 'MongoId')) {

      // identify the schema of the question
      $document = $this->_DB->read('questions', array('_id' => $questionId));
      $schema = $document[key($document)]['schema'];

      # deal with key not in schema
      # deal with key value update not allowed

      if (array_key_exists(key($update), $this->_questionSchema[$schema])) {

        return true;
      }
    }

    return false;
  }

  // TODO: DELETE A QUESTION

  #########################################
  ################# TESTS #################
  #########################################

  // TODO: CREATE A TEST

  // TODO: GET (READ) TESTS

  /**
   *  GET (READ) TESTS
   *  Get all tests matching a user ID
   *  @return document(s) as PHP array (empty if no docs)
   */
  public function getTests($userId) {

    // TODO

    return false;
  }

  // TODO: UPDATE A TEST

  // TODO: DELETE A TEST
}
