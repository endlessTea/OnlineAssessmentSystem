<?php

/**
 *  QUESTIONSCHEMATEST.PHP
 *  @author Jonathan Lamb
 */
class QuestionSchemaTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_QuestionSchema,
    $_schemaNames,
    $_booleanSchema;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_QuestionSchema = new QuestionSchema();

    // define schemas (match to values contained in QuestionSchema class)
    $this->_schemaNames = array(
      "boolean"
    );
    $this->_booleanSchema = array(
      "schema" => "required",
      "author" => "required",
      "statement" => "required",
      "singleAnswer" => "required",
      "feedbackCorrect" => "optional",
      "feedbackIncorrect" => "optional"
    );
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of QuestionSchema Test -  \n");
  }

  /**
   *  @test
   *  Check if list of schema names is returned correctly
   */
  public function getSchemaList_checkReturnsCorrectly_valuesMatch() {

    $this->assertSame(
      $this->_schemaNames,
      $this->_QuestionSchema->getSchemaList()
    );
  }

  /**
   *  @test
   *  Check if single existing schema returns correctly
   */
  public function getSchema_checkReturnsCorrectly_valuesMatch() {

    $this->assertSame(
      $this->_booleanSchema,
      $this->_QuestionSchema->getSchema("boolean")
    );
  }

  /**
   *  @test
   *  Attempt to obtain a schema that does not exist
   */
  public function getSchema_attemptToGetInexistentSchema_methodReturnsFalse() {

    $this->assertFalse($this->_QuestionSchema->getSchema("megaSchema"));
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of QuestionSchema Test -  \n\n");
  }
}
