<?php

/**
 *  QUESTIONSCHEMA.PHP
 *  Defines the structure of different types of 'question' documents
 *  @author Jonathan Lamb
 */
class QuestionSchema {

  // store accepted question types as instance variable
  private $_schema;

  /**
   *  Constructor
   *  Initialise instance variables (define schema here)
   */
  public function __construct() {

    // define question schema
    $this->_schema = array(
      "boolean" => array(
        "schema" => "required",
        "name" => "required",
        "author" => "required",
        "question" => "required",
        "singleAnswer" => "required",
        "feedback" => "optional"
      ),
      "multiple" => array(
        "schema" => "required",
        "name" => "required",
        "author" => "required",
        "question" => "required",
        "options" => "required",
        "correctAnswers" => "required",
        "feedback" => "optional"
      ),
      "pattern" => array(
        "schema" => "required",
        "name" => "required",
        "author" => "required",
        "question" => "required",
        "pattern" => "required",
        "feedback" => "optional"
      ),
      "short" => array(
        "schema" => "required",
        "name" => "required",
        "author" => "required",
        "question" => "required",
        "answer" => "required",
        "feedback" => "optional"
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
