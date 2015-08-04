<?php

/**
 *  ASSESSMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AssessModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variables
  private $_DB,
    $_AssessModel,
    $_UserModel;

  /**
   *  Constructor
   *  Initialise instance variables and create sample test, keeping references to common variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_AssessModel = new AssessModel();
    $this->_UserModel = new UserModel();
  }

  /**
   *  @test
   *  Confirm start AND create database entries ONCE only
   */
  public function _confirmStart() {
    print_r(" - start of AssessModel Test -  \n");

    // create users
    $this->_UserModel->createUser("testAuthor", "password");
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->createUser("testStudent", "password");
    $this->_UserModel->findUser("testStudent");
    $studentIdAvailable = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->createUser("testStudent2", "password");
    $this->_UserModel->findUser("testStudent2");
    $studentIdTaken = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->createUser("testStudent3", "password");

    // create new questions
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "This sentence contains no vowels",
      "singleAnswer" => "FALSE"
    ));
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "This sentence contains 10 vowels",
      "singleAnswer" => "TRUE"
    ));
    $this->_DB->create("questions", array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "This sentence contains a jam sandwich",
      "singleAnswer" => "FALSE"
    ));

    // get the question id's
    $documents = $this->_DB->read("questions", array("author" => $authorId));

    // create a test
    $this->assertTrue($this->_DB->create("tests", array(
      "schema" => "standard",
      "author" => $authorId,
      "questions" => array_keys($documents)
    )));

    // get the test id
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));

    // register the student id with the test
    $this->assertTrue($this->_DB->update(
      "tests",
      array("_id" => new MongoId($testId)),
      array("available" => array($studentIdAvailable)
    )));

    // update test with example user that would have taken the test
    $this->assertTrue($this->_DB->update(
      "tests",
      array("_id" => new MongoId($testId)),
      array("taken" => array($studentIdTaken => "3")
    )));
  }

  /**
   *  @test
   *  Check if a user is eligible to take a test
   */
  public function checkTestAvailability_checkWithValidStudent_methodReturnsTrue() {

    // get author, test and student id
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->_UserModel->findUser("testStudent");
    $studentIdReady = $this->_UserModel->getUserData()->userId;

    $result = $this->_AssessModel->checkTestAvailability(
      new MongoId($testId),
      $studentIdReady
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check if a user that is not registered with a test is eligible
   */
  public function checkTestAvailability_checkWithIneligibleStudent_methodReturnsFalse() {

    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->_UserModel->findUser("testStudent3");
    $studentIdNotRegistered = $this->_UserModel->getUserData()->userId;

    $result = $this->_AssessModel->checkTestAvailability(
      new MongoId($testId),
      $studentIdNotRegistered
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Check if a user that has already taken the test is not eligible
   */
  public function checkTestAvailability_checkWithStudentTakenTest_methodReturnsFalse() {

    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->_UserModel->findUser("testStudent2");
    $studentIdTaken = $this->_UserModel->getUserData()->userId;

    $result = $this->_AssessModel->checkTestAvailability(
      new MongoId($testId),
      $studentIdTaken
    );
    $this->assertSame(
      "User '{$studentIdTaken}' has already taken this test.",
      $result
    );
  }

  /**
   *  @expectedException
   *  Attempt to start test with no test loaded
   */
  public function startTestGetJSONData_attemptToStartWithNoTest_methodThrowsException() {

    $this->_AssessModel->startTestGetJSONData();
  }

  /**
   *  @expectedException
   *  Attempt to convert test questions that have not been initialised
   */
  public function convertQuestionsToJSON_attemptToConvertNoQuestions_methodThrowsException() {

    $this->_AssessModel->convertQuestionsToJSON();
  }

  /**
   *  @test
   *  Check if AssessModel correctly loads a test as an instance variable
   */
  public function loadTest_loadAsInstanceVariable_methodReturnsTrue() {

    $this->_UserModel->findUser("testStudent");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->assertTrue($this->_AssessModel->loadTest(new MongoId($testId), $studentId));
  }

  /**
   *  @expectedException
   *  Attempt to load test with invalid test identifier
   */
  public function loadTest_attemptInvalidLoad_methodThrowsException() {

    $this->_UserModel->findUser("testStudent");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_AssessModel->loadTest('9023hngf3902n902fnf923np', $studentId);
  }

  /**
   *  @test
   *  Convert questions to JSON format
   */
  public function convertQuestionsToJSON_convertValidQuestions_methodReturnsTrue() {

    // TODO complete me
  }

  /*
   *  @test
   *  Start the test loaded as instance variable in AssessModel

  public function startTestGetJSONData_startNewTest_methodReturnsTrue() {

    $this->_UserModel->findUser("testStudent");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->_AssessModel->loadTest(new MongoId($testId), $studentId);

    // TODO complete me

    $result = $this->_AssessModel->startTestGetJSONData();
    $this->assertSame(
      "",
      $result
    );
  }
  */

  /**
   *  @test
   *  Attempt to start test that has already been started
   */
  public function startTestGetJSONData_attemptToStartTestAlreadyStarted_methodReturnsFalse() {

    $this->_UserModel->findUser("testStudent");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $authorId)));
    $this->_AssessModel->loadTest(new MongoId($testId), $studentId);
    $result = $this->_AssessModel->startTestGetJSONData();

    $this->assertFalse($this->_AssessModel->startTestGetJSONData());
  }

  /**
   *  @test
   *  Drop Questions, Tests and Users collections (reset for later testing)
   */
  public function _dropCollections_methodsReturnTrue() {

    $dropQuestionsResult = $this->_DB->delete("questions", "DROP COLLECTION");
    $dropTestsResult = $this->_DB->delete("tests", "DROP COLLECTION");
    $dropUsersResult = $this->_DB->delete("users", "DROP COLLECTION");
    $this->assertTrue($dropQuestionsResult && $dropTestsResult && $dropUsersResult);
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of AssessModel Test -  \n\n");
  }
}
