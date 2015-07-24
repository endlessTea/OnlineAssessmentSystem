<?php

class DBTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_DB;
  private $_sampleDocumentOne;
  private $_sampleDocumentTwo;
  private $_sampleDocumentThree;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_sampleDocumentOne = array(
      'name' => 'sample one',
      'array' => array(
        'arrayValueOne' => 1234,
        'arrayValueTwo' => 'a String'
      )
    );
    $this->_sampleDocumentTwo = array(
      'name' => 'sample two',
      'extra' => 'document extra property',
      'array' => array(
        'arrayValueOne' => 'another String',
        'arrayValueTwo' => 5678,
        'arrayValueThree' => 'abcdefg'
      )
    );
    $this->_sampleDocumentThree = array(
      'name' => 'sample three',
      'extra' => 'document extra property'
    );
  }

  /**
   *  @test
   */
  public function getInstance_callMethodTwice_sameObjectReturned() {

    $dbRefTwo = DB::getInstance();
    $this->assertEquals($this->_DB, $dbRefTwo);
  }

  /**
   *  @test
   *  Insert a single document (valid collection)
   */
  public function create_insertSingleDocument_returnsTrue() {

    $result = $this->_DB->create('questions', $this->_sampleDocumentOne);
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Insert multiple documents (valid collection)
   */
  public function create_insertMultipleDocuments_returnsTrue() {

    $result = $this->_DB->create('questions', array(
      $this->_sampleDocumentTwo,
      $this->_sampleDocumentThree,
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt single document insert to an invalid collection
   */
  public function create_attemptInsertWithInvalidCollection_returnsSpecificString() {

    $result = $this->_DB->create('bananas', $this->_sampleDocumentOne);
    $this->assertSame(
      '\'bananas\' is an invalid collection',
      $result
    );
  }

  /**
   *  @test
   *  Drop collection; delete all documents inserted
   */
  public function delete_dropCollection_returnsTrue() {

    $result = $this->_DB->delete('questions');
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt drop of invalid collection
   */
  public function delete_dropInvalidCollection_returnsSpecificString() {

    $result = $this->_DB->delete('bananas');
    $this->assertSame(
      '\'bananas\' is an invalid collection',
      $result
    );
  }
}
