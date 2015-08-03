<?php

/**
 *  ASSESSMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AssessModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variables
  private $_DB,
    $_AssessModel,
    $_authorId,
    $_questionIds,
    $_testId,
    $_studentId;

  /**
   *  Constructor
   *  Initialise instance variables and create sample test, keeping references to common variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_AssessModel = new AssessModel();
    $this->_authorId = "y890o3htnbwohe9832r2209f";
    $this->_studentId = "321498riufbgkibfuiuesb";

    // create new questions
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $this->_authorId,
      "statement" => "This sentence contains no vowels",
      "singleAnswer" => "FALSE"
    ));
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $this->_authorId,
      "statement" => "This sentence contains 10 vowels",
      "singleAnswer" => "TRUE"
    ));
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $this->_authorId,
      "statement" => "This sentence contains a jam sandwich",
      "singleAnswer" => "FALSE"
    ));

    // get the question id's and store
    $documents = $document = $this->_DB->read("questions", array("author" => $this->_authorId));
    $this->_questionIds = array_keys($documents);

    // create a test
    $this->_DB->create("tests", array(
      "schema" => "standard",
      "author" => $this->_authorId,
      "questions" => $this->_questionIds
    ));

    // get the test id
    $document = $this->_DB->read("tests", array("author" => $this->_authorId));
    $this->_testId = key($document);

    // register the student id with the test TODO
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of AssessModel Test -  \n");
  }

  /**
   *  @test
   *  Check if a user is eligible to take a test
   */
  public function checkTestAvailability_checkWithValidStudent_methodReturnsTrue() {

    // TODO
  }

  /**
   *  @test
   *  Check if a user is not eligible to take a test
   */
  public function checkTestAvailability_checkWithInvalidStudent_methodReturnsFalse() {

    // TODO
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of AssessModel Test -  \n\n");
  }
}
