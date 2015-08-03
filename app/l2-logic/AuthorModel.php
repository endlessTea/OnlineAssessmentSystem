<?php

/**
 *  AUTHORMODEL.PHP
 *  Create, get, update and delete questions and tests
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
      "boolean" => array(
        "schema" => "required",
        "author" => "required",
        "statement" => "required",
        "singleAnswer" => "required",
        "feedbackCorrect" => "optional",
        "feedbackIncorrect" => "optional"
      )
    );

    // define test schema: restrict document structure of tests
    $this->_testSchema = array(
      "standard" => array(
        "schema" => "required",
        "author" => "required",
        "questions" => "required"
      )
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
  public function createQuestion($question = array()) {

    // fail the operation if the user did not provide a schema key, otherwise check if schema is recognised
    if (!isset($question["schema"])) return false;
    if (array_key_exists($question["schema"], $this->_questionSchema)) {

      // prepare document to insert (valid data will be transfered to this new variable)
      $document = array();

      // loop through each schema property
      foreach ($this->_questionSchema[$question["schema"]] as $sProperty => $sRequirement) {

        // if a required property was not provided, fail the operation
        if ($sRequirement === "required" && !isset($question[$sProperty])) return false;

        // if property is required or property is optional AND there is a value that can be used
        if ($sRequirement === "required" ||
         ($sRequirement === "optional" && isset($question[$sProperty]))) {

          // copy item from the question to the insertion document and remove it from question array
          $document[$sProperty] = $question[$sProperty];
          unset($question[$sProperty]);
        }
      }

      // any remaining data is not part of the schema, fail the operation
      if (!empty($question)) return false;

      // otherwise the question provided was valid, return the result of the DB operation
      return $this->_DB->create("questions", $document);
    }

    return false;
  }

  /**
   *  GET (READ) QUESTIONS
   *  Get all questions matching a user ID
   *  @return document(s) as PHP array (empty if no docs)
   */
  public function getQuestions($userIdStr) {

    // check userId contains hexadecimal characters only (fail if otherwise)
    if (preg_match('/^([a-z0-9])+$/', $userIdStr) === 0) return false;

    // return data
    return $this->_DB->read("questions", array("author" => $userIdStr));
  }

  /**
   *  UPDATE A QUESTION
   *  Update the value of a single question (expects MongoId object)
   *  If key exists in schema and operation is permitted
   *  @return true (boolean) on success, else false
   */
  public function updateQuestion($questionIdObj, $update = array()) {

    // check questionIdObj is MongoId object
    if (is_a($questionIdObj, 'MongoId')) {

      // identify the schema of the question
      $document = $this->_DB->read("questions", array("_id" => $questionIdObj));
      $schema = $document[key($document)]["schema"];

      // only continue if the update complies with the schema AND it isn"t an author update
      if (array_key_exists(key($update), $this->_questionSchema[$schema])
        && key($update) !== "author") {

        // return the result of the update operation
        return $this->_DB->update("questions", array("_id" => $questionIdObj), $update);
      }
    }

    return false;
  }

  /**
   *  DELETE A QUESTION
   *  Delete a single question (expects MongoId object) if it is the author's question
   *  @return true (boolean) on success, else false
   */
  public function deleteQuestion($questionIdObj, $authorIdStr) {

    // check questionIdObj is MongoId object
    if (is_a($questionIdObj, 'MongoId')) {

      // identify the author of the question
      $document = $this->_DB->read("questions", array("_id" => $questionIdObj));
      $author = $document[key($document)]["author"];

      // permit delete operation if the author ID matches
      if ($authorIdStr === $author) {

        // return the result of the delete operation
        return $this->_DB->delete("questions", array("_id" => $questionIdObj));
      }
    }

    return false;
  }

  #########################################
  ################# TESTS #################
  #########################################

  /**
   *  CREATE A TEST
   *  Create a test if the schema is valid and the test values follow schema requirements
   *  @return true (boolean) on success, else false
   */
  public function createTest($test = array()) {

    // fail the operation if the user did not provide a schema key, otherwise check if schema is recognised
    if (!isset($test["schema"])) return false;
    if (array_key_exists($test["schema"], $this->_testSchema)) {

      // prepare document to insert (valid data will be transfered to this new variable)
      $document = array();

      // loop through each schema property
      foreach ($this->_testSchema[$test["schema"]] as $tProperty => $tRequirement) {

        // if a required property was not provided, fail the operation
        if ($tRequirement === "required" && !isset($test[$tProperty])) return false;

        // if property is required or property is optional AND there is a value that can be used
        if ($tRequirement === "required" ||
         ($tRequirement === "optional" && isset($test[$tProperty]))) {

          // copy item from the question to the insertion document and remove it from question array
          $document[$tProperty] = $test[$tProperty];
          unset($test[$tProperty]);
        }
      }

      // any remaining data is not part of the schema, fail the operation
      if (!empty($test)) return false;

      // add arrays for making tests available to users and keeping track of who has taken them
      $document["available"] = array();
      $document["taken"] = array();

      // otherwise the test provided was valid, return the result of the DB operation
      return $this->_DB->create("tests", $document);
    }

    return false;
  }

  /**
   *  GET (READ) TESTS
   *  Get all tests matching a user ID
   *  @return document(s) as PHP array (empty if no docs)
   */
  public function getTests($userIdStr) {

    // check userId contains hexadecimal characters only (fail if otherwise)
    if (preg_match('/^([a-z0-9])+$/', $userIdStr) === 0) return false;

    // return data
    return $this->_DB->read("tests", array("author" => $userIdStr));
  }

  /**
   *  UPDATE A TEST
   *  Update the value of a test (expects MongoId object)
   *  If key exists in schema and operation is permitted
   *  @return true (boolean) on success, else false
   */
  public function updateTest($testIdObj, $update = array()) {

    // check testIdObj is MongoId object
    if (is_a($testIdObj, 'MongoId')) {

      // identify the schema of the test
      $document = $this->_DB->read("tests", array("_id" => $testIdObj));
      $schema = $document[key($document)]["schema"];

      // if update contains questions and its value is not an array, fail the operation
      if (isset($update["questions"]))
        if (!is_array($update["questions"])) return false;

      // only continue if update complies with schema AND it isn"t an author update
      if (array_key_exists(key($update), $this->_testSchema[$schema])
        && key($update) !== "author") {

          // return the result of the update operation
          return $this->_DB->update("tests", array("_id" => $testIdObj), $update);
      }
    }

    return false;
  }

  /**
   *  MAKE A TEST AVAILABLE TO A USER
   *
   *  @return
   */
  public function makeTestAvailableToUser($testIdObj, $studentIdObj) {

    // check testIdObj and studentIdObj are MongoIds
    if (is_a($testIdObj, 'MongoId') && is_a($studentIdObj, 'MongoId')) {



    }

    return false;
  }

  /**
   *  DELETE A TEST
   *  Delete a single test (expects MongoId object) if it is the author's test
   *  @return true (boolean) on success, else false
   */
  public function deleteTest($testIdObj, $authorIdStr) {

    // check testIdObj is MongoId object
    if (is_a($testIdObj, 'MongoId')) {

      // identify the author of the question
      $document = $this->_DB->read("tests", array("_id" => $testIdObj));
      $author = $document[key($document)]["author"];

      // permit delete operation if the author ID matches
      if ($authorIdStr === $author) {

        // return the result of the delete operation
        return $this->_DB->delete("tests", array("_id" => $testIdObj));
      }
    }
    
    return false;
  }
}
