<?php

/**
 *  TESTSCHEMA.PHP
 *  Defines the structure of different types of 'test' documents
 *  @author Jonathan Lamb
 */
class TestSchema {

  // store accepted test types as instance variable
  private $_schema;

  /**
   *  Constructor
   *  Initialise instance variables (define schema here)
   */
  public function __construct() {

    // define test schema
    $this->_schema = array(
      "standard" => array(
        "schema" => "required",
        "author" => "required",
        "questions" => "required"
      )
    );
  }

  /**
   *  GET LIST OF AVAILABLE SCHEMAS
   *  @return array of available schema types
   */
  public function getSchemaList() {

    return array_keys($this->_schema);
  }

  /**
   *  GET FULL SCHEMA
   *  @return array of definitions for a specific schema, else return false (not recognised)
   */
  public function getSchema($schemaName) {

    if (array_key_exists($schemaName, $this->_schema)) {

      return $this->_schema[$schemaName];
    }

    return false;
  }
}
