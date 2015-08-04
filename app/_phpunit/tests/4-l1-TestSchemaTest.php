<?php

/**
 *  TESTSCHEMATEST.PHP
 *  @author Jonathan Lamb
 */
class TestSchemaTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_TestSchema,
    $_schemaNames,
    $_standardSchema;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_TestSchema = new TestSchema();

    // define schemas (match to values contained in QuestionSchema class)
    $this->_schemaNames = array(
      "standard"
    );
    $this->_standardSchema = array(
      "schema" => "required",
      "author" => "required",
      "questions" => "required"
    );
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of TestSchema Test -  \n");
  }

  /**
   *  @test
   *  Check if list of schema names is returned correctly
   */
  public function getSchemaList_checkReturnsCorrectly_valuesMatch() {

    $this->assertSame(
      $this->_schemaNames,
      $this->_TestSchema->getSchemaList()
    );
  }

  /**
   *  @test
   *  Check if single existing schema returns correctly
   */
  public function getSchema_checkReturnsCorrectly_valuesMatch() {

    $this->assertSame(
      $this->_standardSchema,
      $this->_TestSchema->getSchema("standard")
    );
  }

  /**
   *  @test
   *  Attempt to obtain a schema that does not exist
   */
  public function getSchema_attemptToGetInexistentSchema_methodReturnsFalse() {

    $this->assertFalse($this->_TestSchema->getSchema("superSchema"));
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of TestSchema Test -  \n\n");
  }
}
