<?php

class DBTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_DB;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
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
   */
  public function getCollection_getUsers_returnsReferenceNotNull() {

    $reference = $this->_DB->getCollection('users');
    $this->assertTrue($reference != null);
  }

  /**
   *  @expectedException InvalidArgumentException
   */
  public function getCollection_getInvalidCollection_throwsException() {

    $reference = $this->_DB->getCollection('bananas');
  }

  // add a document to the collection
}
