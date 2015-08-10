<?php

/**
 *  DB.PHP
 *  Support a single, sharable connection to a MongoDB database.
 *  Constrain operations with create, read, update and delete (CRUD) methods.
 *  @author Jonathan Lamb
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
        "users", "questions", "tests", "samples"
      );

      // create unique index for users based on username
      $this->_mongo->users->createIndex(
        array("user_name" => 1),
        array("unique" => true)
      );

    } catch (Exception $e) {
      die($e->getMessage());
    }
  }

  /**
   *  Get Instance
   *  Control instantiation of DB objects by creating one only
   *  @return instance of the DB connection to MongoDB
   */
  public static function getInstance() {

    // check if instance has not been created yet
    if (!isset(self::$_instance)) {
      self::$_instance = new DB();
    }

    return self::$_instance;
  }

  /**
   *  Get collection (private method)
   *  @return reference to known collection
   *  @throws InvalidArgumentException for unrecognised collections
   */
  private function getCollection($collection) {

    if (in_array($collection, self::$_validCollections, true)) {

      return $this->_mongo->$collection;

    } else throw new InvalidArgumentException(
      "'{$collection}' is an invalid collection"
    );
  }

  /**
   *  Check if a variable is a two-dimensional array
   *  @author Vinko Vrsalovic
   *  @link http://stackoverflow.com/questions/145337/checking-if-array-is-multidimensional-or-not
   *  @license None
   *  @return true (boolean) if variable is multi-dimensional, else returns false
   */
  private function is2DArray($variable) {

    // check if any sub-item is not an array (return false if so)
    foreach ($variable as $item) {
      if (!is_array($item)) return false;
    }

    // otherwise every sub-item is an array
    return true;
  }

  /**
   *  CREATE
   *  Insert one or more documents into a collection
   *  @return true (boolean) for success, String warning of invalid collections
   */
  public function create($collectionName, $documents) {

    try {

      $collection = $this->getCollection($collectionName);

      // return error if variable is not an array
      if (!is_array($documents)) {

        return "Document variable invalid: supply an array of associate arrays, or a single assoc. array";
      }

      // if 2D array, perform batch insert and return indicator of success
      if ($this->is2DArray($documents)) {

        $collection->batchInsert($documents);
        return true;
      }

      // otherwise perform single document insert, return indicator of success
      $collection->insert($documents);
      return true;

    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   *  READ
   *  Return (select) one or more documents from a collection
   *  @return PHP array of documents matching condition criteria, all documents, or a warning String
   */
  public function read($collectionName, $conditions = null) {

    try {

      $collection = $this->getCollection($collectionName);

      if ($conditions === "ALL DOCUMENTS") {

        return iterator_to_array($collection->find());

      } elseif (is_array($conditions)) {

        return iterator_to_array($collection->find($conditions));
      }

      // if branches were not entered, inform user of error
      return "Read conditions are invalid: supply a valid associative array or 'ALL DOCUMENTS'";

    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   *  UPDATE
   *  Update one or more documents in a collection
   *  @return true (boolean) or a warning String
   */
  public function update($collectionName, $conditions = null, $updates = null) {

    try {

      $collection = $this->getCollection($collectionName);

      if (is_array($conditions) && is_array($updates)) {

        // otherwise update-by-replace where condition is true
        $collection->update($conditions, array('$set' => $updates));
        return true;
      }

      // if branches were not entered, inform user of error
      return "Updates and Conditions are invalid: supply two valid associative arrays";

    } catch (Exception $e) {
      return $e->getMessage();
    }
  }

  /**
   *  DELETE
   *  Delete one or more documents based on certain conditions
   *  Or drop all documents contained in a collection
   *  @return true (boolean) for success, Strings warning of invalid collections or conditions
   */
  public function delete($collectionName, $conditions = null) {

    try {

      $collection = $this->getCollection($collectionName);

      if (is_array($conditions)) {

        // delete where condition is true
        $collection->remove($conditions);
        return true;

      } elseif ($conditions === "DROP COLLECTION") {

        // drop the collection
        $collection->drop();
        return true;
      }

      // if one of the branches did not enter, inform of error
      return "Delete conditions are invalid: supply a valid associative array or 'DROP COLLECTION'";

    } catch (Exception $e) {
      return $e->getMessage();
    }
  }
}
