<?php

/**
 *  ASSESSMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AssessModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variable
  private $_DB,
    $_AssessModel;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_AuthorModel = new AssessModel();
  }

  /**
   *  @test
   *
   */
  public function _replaceMe() {

    $this->assertEquals(1, 1);
  }
}
