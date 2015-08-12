<?php

/**
 *  VISUALSMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class VisualsModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variables
  private $_DB,
    $_VisualsModel,
    $_AssessModel,
    $_UserModel,
    $_AuthorModel;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_VisualsModel = new VisualsModel();
    $this->_AssessModel = new AssessModel();
    $this->_UserModel = new UserModel();
    $this->_AuthorModel = new AuthorModel();
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of VisualsModel Test -  \n");
  }

  /**
   *  @test
   *  Create required MongoDB entries once only
   */
  public function _createMongoDBentries_methodsReturnTrue() {

    // create users
    $this->_UserModel->createUser("testAuthor", "password", "Test Author");
    $this->_UserModel->createUser("studentOne", "password", "Test Student");
    $this->_UserModel->createUser("studentTwo", "password", "Test Student Two");
    $this->_UserModel->createUser("studentNoParticipation", "password", "Non-participating Student");

    // get user id's
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;

    // create new questions
    $resultOne = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "JavaScript is also known as ECMAScript.",
      "singleAnswer" => "TRUE",
      "feedback" => "ECMAScript is the untrademarked name for the language."
    ));
    $resultTwo = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "D3.js is not a data visualisation library.",
      "singleAnswer" => "FALSE",
      "feedback" => "D3.js is a data visualisation library created by Mike Bostock."
    ));
    $resultThree = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "AngularJS is a front-end web application framework.",
      "singleAnswer" => "TRUE",
      "feedback" => "AngularJS is maintained by Google."
    ));
    $resultFour = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "Open-source software is the same as 'free' software.",
      "singleAnswer" => "FALSE",
      "feedback" => "Ask Richard Stallman for further details."
    ));
    $resultFive = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $authorId,
      "statement" => "This question will not be included in any tests",
      "singleAnswer" => "TRUE"
    ));
    $this->assertTrue($resultOne && $resultTwo && $resultThree && $resultFour && $resultFive);

    // get question id's
    foreach ($this->_AuthorModel->getQuestions($authorId) as $qKey => $q) {
      $questionsIds[] = $qKey;
    }

    // create 2x tests
    $resultOne = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "author" => $authorId,
      "questions" => array(
        $questionsIds[0], $questionsIds[1]
      )
    ));
    $resultTwo = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "author" => $authorId,
      "questions" => array(
        $questionsIds[2], $questionsIds[3]
      )
    ));
    $this->assertTrue($resultOne && $resultTwo);

    // get test id's
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    // register both students with both tests
    $resultOne = $this->_AuthorModel->makeTestAvailableToUser(
      new MongoId($testIds[0]),
      new MongoId($studentOne)
    );
    $resultTwo= $this->_AuthorModel->makeTestAvailableToUser(
      new MongoId($testIds[0]),
      new MongoId($studentTwo)
    );
    $resultThree = $this->_AuthorModel->makeTestAvailableToUser(
      new MongoId($testIds[1]),
      new MongoId($studentOne)
    );
    $resultFour = $this->_AuthorModel->makeTestAvailableToUser(
      new MongoId($testIds[1]),
      new MongoId($studentTwo)
    );
    $this->assertTrue($resultOne && $resultTwo && $resultThree && $resultFour);

    // simulate first student taking first test
    $s1t1answers = new stdClass();
    $s1t1answers->{0} = new stdClass();
    $s1t1answers->{0}->{'uq'} = 1;
    $s1t1answers->{0}->{'ans'} = 'TRUE';
    $s1t1answers->{1} = new stdClass();
    $s1t1answers->{1}->{'uq'} = 1;
    $s1t1answers->{1}->{'ans'} = 'FALSE';
    $this->assertSame(
      "{\"score\":2,\"feedback\":{}}",
      $this->_AssessModel->updateAnswers(new MongoId($testIds[0]), $studentOne, $s1t1answers)
    );

    // simulate second student taking first test
    $s2t1answers = new stdClass();
    $s2t1answers->{0} = new stdClass();
    $s2t1answers->{0}->{'uq'} = 0;
    $s2t1answers->{0}->{'ans'} = 'TRUE';
    $s2t1answers->{1} = new stdClass();
    $s2t1answers->{1}->{'uq'} = 1;
    $s2t1answers->{1}->{'ans'} = 'TRUE';
    $this->assertSame(
      "{\"score\":1,\"feedback\":{\"1\":\"D3.js is a data visualisation library created by Mike Bostock.\"}}",
      $this->_AssessModel->updateAnswers(new MongoId($testIds[0]), $studentTwo, $s2t1answers)
    );

    // submit feedback for test
    $s2t1feedback = new stdClass();
    $s2t1feedback->{1} = 1;
    $this->assertTrue($this->_AssessModel->updateFeedback(new MongoId($testIds[0]), $studentOne, $s2t1feedback));

    // simulate first student taking second test
    $s1t2answers = new stdClass();
    $s1t2answers->{0} = new stdClass();
    $s1t2answers->{0}->{'uq'} = 1;
    $s1t2answers->{0}->{'ans'} = 'FALSE';
    $s1t2answers->{1} = new stdClass();
    $s1t2answers->{1}->{'uq'} = 0;
    $s1t2answers->{1}->{'ans'} = 'FALSE';
    $this->assertSame(
      "{\"score\":1,\"feedback\":{\"0\":\"AngularJS is maintained by Google.\"}}",
      $this->_AssessModel->updateAnswers(new MongoId($testIds[1]), $studentOne, $s1t2answers)
    );

    // submit feedback for test
    $s1t2feedback = new stdClass();
    $s1t2feedback->{0} = 0;
    $this->assertTrue($this->_AssessModel->updateFeedback(new MongoId($testIds[1]), $studentOne, $s1t2feedback));

    // simulate second student taking second test
    $s2t2answers = new stdClass();
    $s2t2answers->{0} = new stdClass();
    $s2t2answers->{0}->{'uq'} = 0;
    $s2t2answers->{0}->{'ans'} = 'FALSE';
    $s2t2answers->{1} = new stdClass();
    $s2t2answers->{1}->{'uq'} = 1;
    $s2t2answers->{1}->{'ans'} = 'TRUE';
    $this->assertSame(
      "{\"score\":0,\"feedback\":{\"0\":\"AngularJS is maintained by Google.\",\"1\":\"Ask Richard Stallman for further details.\"}}",
      $this->_AssessModel->updateAnswers(new MongoId($testIds[1]), $studentTwo, $s2t2answers)
    );

    // submit feedback for test
    $s2t2feedback = new stdClass();
    $s2t2feedback->{0} = 1;
    $s2t2feedback->{1} = 0;
    $this->assertTrue($this->_AssessModel->updateFeedback(new MongoId($testIds[1]), $studentTwo, $s2t2feedback));

    //print_r($this->_AuthorModel->getTests($authorId));
    //print_r($this->_AuthorModel->getQuestions($authorId));
  }

  /**
   *  @test
   *  Check the performance of a single student on a single question
   */
  public function getStudentPerformanceSingleQuestion_validRequest_methodReturnsMatchingJSON() {

    // get student one's id and get the first question's id
    $this->_UserModel->findUser("studentOne");
    $studentId = $this->_UserModel->getUserData()->userId;
    $questionId = key($this->_DB->read("questions", array("statement" => "JavaScript is also known as ECMAScript.")));

    $this->assertSame(
      "{\"{$studentId}\":1}",
      $this->_VisualsModel->getStudentPerformanceSingleQuestion(new MongoId($questionId), $studentId)
    );
  }

  /**
   *  @test
   *  Attempt to get student data for a question they have not taken
   */
  public function getStudentPerformanceSingleQuestion_studentHasntTakenQuestion_methodReturnsFalse() {

    // get student one's id and get the untaken question id
    $this->_UserModel->findUser("studentOne");
    $studentId = $this->_UserModel->getUserData()->userId;
    $questionId = key($this->_DB->read("questions", array("statement" => "This question will not be included in any tests")));

    $this->assertFalse(
      $this->_VisualsModel->getStudentPerformanceSingleQuestion(new MongoId($questionId), $studentId)
    );
  }

  /**
   *  @test
   *  Check the performance of a single student on a single test
   */
  public function getStudentPerformanceSingleTest_validRequest_methodReturnsMatchingJSON() {

    // get student two's id and get the second test id (using author id)
    $this->_UserModel->findUser("studentTwo");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    $this->assertSame(
      "{\"{$studentId}\":0}",
      $this->_VisualsModel->getStudentPerformanceSingleTest(new MongoId($testIds[1]), $studentId)
    );
  }

  /**
   *  @test
   *  Attempt to get student data for a test they have not taken
   */
  public function getStudentPerformanceSingleTest_studentHasntTakenTest_methodReturnsFalse() {

    // get student one's id and get the untaken question id
    $this->_UserModel->findUser("studentNoParticipation");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    $this->assertFalse(
      $this->_VisualsModel->getStudentPerformanceSingleTest(new MongoId($testIds[0]), $studentId)
    );
  }

  /**
   *  @test
   *  Check the performance of a single student for all tests they have taken
   */
  public function getStudentPerformanceAllTests_validRequest_methodReturnsMatchingJSON() {

    // student one: both tests
    $this->_UserModel->findUser("studentOne");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    $this->assertSame(
      "{\"{$testIds[0]}\":2,\"{$testIds[1]}\":1}",
      $this->_VisualsModel->getStudentPerformanceAllTests($studentId)
    );
  }

  /**
   *  @test
   *  Attempt to get student data when they have taken no tests
   */
  public function getStudentPerformanceAllTests_studentHasntTakenAnyTests_methodReturnsFalse() {

    $this->_UserModel->findUser("studentNoParticipation");
    $studentId = $this->_UserModel->getUserData()->userId;
    $this->assertFalse(
      $this->_VisualsModel->getStudentPerformanceAllTests($studentId)
    );
  }

  /**
   *  @test
   *  Check the performance of all students (class) for a single question
   */
  public function getClassPerformanceSingleQuestion_validRequest_methodReturnsMatchingJSON() {

    // get user and question id's
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $questionId = key($this->_DB->read("questions", array("statement" => "AngularJS is a front-end web application framework.")));
    $this->assertSame(
      "{\"{$studentOne}\":0,\"{$studentTwo}\":0}",
      $this->_VisualsModel->getClassPerformanceSingleQuestion(new MongoId($questionId))
    );
  }

  /**
   *  @test
   *  Attempt to get performance information for a question that has not been taken
   */
  public function getClassPerformanceSingleQuestion_questionNotTaken_methodReturnsFalse() {

    $questionId = key($this->_DB->read("questions", array("statement" => "This question will not be included in any tests")));
    $this->assertFalse($this->_VisualsModel->getClassPerformanceSingleQuestion(new MongoId($questionId)));
  }

  /**
   *  @test
   *  Check the performance of all students (class) for a single test
   */
  public function getClassPerformanceSingleTest_validRequest_methodReturnsMatchingJSON() {

    // get user and test id's
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    // check both tests
    $this->assertSame(
      "{\"{$studentOne}\":2,\"{$studentTwo}\":1}",
      $this->_VisualsModel->getClassPerformanceSingleTest(new MongoId($testIds[0]))
    );
    $this->assertSame(
      "{\"{$studentOne}\":1,\"{$studentTwo}\":0}",
      $this->_VisualsModel->getClassPerformanceSingleTest(new MongoId($testIds[1]))
    );
  }

  /**
   *  @test
   *  Check the performance of all students for all tests
   */
  public function getClassPerformanceAllTests_validRequest_methodReturnsMatchingJSON() {

    // get user and test id's
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    foreach ($this->_AuthorModel->getTests($authorId) as $tKey => $t) {
      $testIds[] = $tKey;
    }

    // check all tests returned
    $this->assertSame(
      "{\"{$testIds[0]}\":{\"{$studentOne}\":2,\"{$studentTwo}\":1}," .
      "\"{$testIds[1]}\":{\"{$studentOne}\":1,\"{$studentTwo}\":0}}",
      $this->_VisualsModel->getClassPerformanceAllTests()
    );
  }

  /*
   *  @test
   *  TODO: feedback tests
   */
  /*
   *  @test
   *  TODO: feedback tests
   */
  /*
   *  @test
   *  TODO: feedback tests
   */

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
    print_r("\n  - end of VisualsModel Test -  \n\n");
  }
}
