<?php

/**
 *  DB.PHP
 *  Support a single, sharable connection to a MongoDB database.
 *  Constrain operations with create, read, update and delete (CRUD) methods.
 */
class DB {

  // establish database connection as a singleton
  private static $_instance = null;
  private static $_validCollections = null;

  // instance variables
  private $_mongo;

  /**
   *  Private Constructor (use static factory method 'getInstance' instead)
   */
  private function __construct() {

    try {

      // connect with MongoClient, create/use database, store connection reference
      $connection = new MongoClient('mongodb://' . $GLOBALS['config']['mongodb']['host']);
      $dbname = $GLOBALS['config']['mongodb']['db'];
      $this->_mongo = $connection->$dbname;

      // define valid collections
      self::$_validCollections = array(
        'users', 'questions', 'tests'
      );

    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   *  Get Instance
   *  Control instantiation of DB objects by creating one only
   */
  public static function getInstance() {

    // check if instance has not been created yet
    if (!isset(self::$_instance)) {
      self::$_instance = new DB();
    }

    return self::$_instance;
  }

  /**
   *  Get collection
   *  Return references to known collections only
   *  Throw InvalidArgumentException for unrecognised collections
   */
  public function getCollection($collection) {

    if (in_array($collection, self::$_validCollections, true)) {

      return $this->_mongo->$collection;

    } else throw new InvalidArgumentException('Invalid Collection');
  }
}
