<?php

/**
 *  USERMODEL.PHP
 *  Responsible for logging users in, registration and returning user data
 *  Model class contains many methods from the 'User' class of PHP Academy's OOP Login System
 *  @author original: PHP Academy ('User' class); modified: Jonathan Lamb
 *  @link https://github.com/adamaoc/login_reg
 *  @license None
 */
class UserModel {

  // store DB utility, update restrictions, user data and login status as instance variables
  private $_DB,
    $_SG,
    $_updateProperties,
    $_userData,
    $_loginStatus;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations and SG access object
    $this->_DB = DB::getInstance();
    $this->_SG = new SG();

    // define which fields may be updated
    $this->_updateProperties = array(
      "hash", "salt"
    );

    // if an existing session exists..
    if ($this->_SG->session("user", "exists")) {

      // attempt to convert to MongoId Object and check if the user exists..
      try {

        $mongoIdObj = new MongoId($this->_SG->session("user", "get"));
        if ($this->findUser($mongoIdObj)) {

          // indicate that the current user is logged in
          $this->_loginStatus = true;
        }

      } catch (Exception $e) {
        $this->_loginStatus = false;
      }

    } else {

      $this->_loginStatus = false;
    }
  }

  /**
   *  GENERATE SALT
   *  @return 32 random hexademical characters
   */
  public function makeSalt() {
    return bin2hex(mcrypt_create_iv(16));
  }

  /**
   *  GENERATE PASSWORD HASH
   *  @return 64 character hash using input string and salt
   */
  public function makeHash($string, $salt = "") {
    return hash("sha256", $string . $salt);
  }

  /**
   *  CREATE NEW USER
   *  Contains: MongoID (generated), username, hash and salt
   *  @return true (boolean) on success, otherwise an error String
   */
  public function createUser($username, $password) {

    // create salt, then create hash of original password
    $salt = $this->makeSalt();
    $hash = $this->makeHash($password, $salt);

    // create user object to store as BSON document in DB
    $user = array(
      "username" => $username,
      "hash" => $hash,
      "salt" => $salt
    );

    // attempt to create user
    $result = $this->_DB->create("users", $user);

    if (preg_match("/E11000/", $result) === 1) {

      return "Duplicate key: The username '{$username}' already exists.";
    }

    return $result;
  }

  /**
   *  CHECK (FIND) IF A USER EXISTS BASED ON ID
   *  Find a user in MongoDB based on an identifier (MongoId or username)
   *  Used to populate $_userData variable on success
   *  @return true (boolean) if userId is valid, i.e. user exists, else false
   */
  public function findUser($userIdentifier) {


    if (is_a($userIdentifier, "MongoId")) {

      $data = $this->_DB->read("users", array("_id" => $userIdentifier));

    } elseif ($userIdentifier !== null) {

      $data = $this->_DB->read("users", array("username" => $userIdentifier));
    }

    // if the user exists, change $_userData values and return true
    if (count($data) === 1) {
      $this->_userData = new stdClass();
      $this->_userData->userId = key($data);
      $data = array_pop($data);
      $this->_userData->username = $data["username"];
      $this->_userData->hash = $data["hash"];
      $this->_userData->salt = $data["salt"];
      return true;
    }

    return false;
  }

  /**
   *  UPDATE USER FIELD
   *  Permits limited user fields to be updated
   *  @return true (boolean) if operation is successful, else return false
   */
  public function updateUser($property, $value) {

    // if user is logged in
    if ($this->_loginStatus) {

      // if update property is allowed to be changed
      if (in_array($property, $this->_updateProperties, true)) {

        // update current user"s values
        $mongoIdObj = new MongoId($this->_userData->userId);
        return $this->_DB->update("users", array("_id" => $mongoIdObj), array($property => $value));
      }
    }

    return false;
  }

  /**
   *  LOG USER IN
   *  Creates a hash of the password provided as a parameter against the chosen user account"s password
   *  Update Session superglobal on success
   *  @return true (boolean) on success, else false
   */
  public function logUserIn($username, $password) {

    // check if the user exists based on the username (_userData will be populated on success)
    if ($this->findUser($username)) {

      // password check
      if ($this->getUserData()->hash === $this->makeHash($password, $this->getUserData()->salt)) {

        // create session for user, change login status, return true
        $this->_SG->session("user", "put", $this->getUserData()->userId);
        $this->_loginStatus = true;
        return true;
      }
    }

    return false;
  }

  /**
   *  LOG USER OUT
   *  @return true (boolean) on success, else false
   */
  public function logUserOut() {
    return $this->_SG->session("user", "delete");
  }

  /**
   *  GET USER DATA
   *  @return data about the current user logged in
   */
  public function getUserData() {
    return $this->_userData;
  }

  /**
   *  RETURN LOGIN STATUS
   *  @return login status of the current user
   */
  public function getLoginStatus() {
    return $this->_loginStatus;
  }
}
