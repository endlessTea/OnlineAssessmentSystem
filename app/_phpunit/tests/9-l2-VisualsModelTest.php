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
    $this->_UserModel->createUser("testAuthor", "password");
    $this->_UserModel->createUser("studentOne", "password");
    $this->_UserModel->createUser("studentTwo", "password");

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
      "feedback" => "Ask Richard Stallman for futher details."
    ));
    $this->assertTrue($resultOne && $resultTwo && $resultThree && $resultFour);

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
    $resultOne = $this->_AssessModel->loadTest(new MongoId($testIds[0]), $studentOne);
    $this->_AssessModel->startTestGetJSONData();
    $s1t1answers = new stdClass();
    $s1t1answers->{0} = new stdClass();
    $s1t1answers->{0}->{'uq'} = 1;
    $s1t1answers->{0}->{'ans'} = 'TRUE';
    $s1t1answers->{1} = new stdClass();
    $s1t1answers->{1}->{'uq'} = 1;
    $s1t1answers->{1}->{'ans'} = 'FALSE';
    $resultTwo = $this->_AssessModel->updateTestAnswers($s1t1answers);
    $this->assertTrue($resultOne && $resultTwo);

    // simulate second student taking first test
    $resultOne = $this->_AssessModel->loadTest(new MongoId($testIds[0]), $studentTwo);
    $this->_AssessModel->startTestGetJSONData();
    $s2t1answers = new stdClass();
    $s2t1answers->{0} = new stdClass();
    $s2t1answers->{0}->{'uq'} = 0;
    $s2t1answers->{0}->{'ans'} = 'TRUE';
    $s2t1answers->{1} = new stdClass();
    $s2t1answers->{1}->{'uq'} = 1;
    $s2t1answers->{1}->{'ans'} = 'TRUE';
    $resultTwo = $this->_AssessModel->updateTestAnswers($s2t1answers);
    $this->assertTrue($resultOne && $resultTwo);

    // submit feedback for test
    $s2t1feedback = new stdClass();
    $s2t1feedback->{1} = 1;
    $this->assertTrue($this->_AssessModel->updateFeedbackFromStudent($s2t1feedback));

    // simulate first student taking second test
    $resultOne = $this->_AssessModel->loadTest(new MongoId($testIds[1]), $studentOne);
    $this->_AssessModel->startTestGetJSONData();
    $s1t2answers = new stdClass();
    $s1t2answers->{0} = new stdClass();
    $s1t2answers->{0}->{'uq'} = 1;
    $s1t2answers->{0}->{'ans'} = 'FALSE';
    $s1t2answers->{1} = new stdClass();
    $s1t2answers->{1}->{'uq'} = 0;
    $s1t2answers->{1}->{'ans'} = 'FALSE';
    $resultTwo = $this->_AssessModel->updateTestAnswers($s1t2answers);
    $this->assertTrue($resultOne && $resultTwo);

    // submit feedback for test
    $s1t2feedback = new stdClass();
    $s1t2feedback->{0} = 0;
    $this->assertTrue($this->_AssessModel->updateFeedbackFromStudent($s1t2feedback));

    // simulate second student taking second test
    $resultOne = $this->_AssessModel->loadTest(new MongoId($testIds[1]), $studentTwo);
    $this->_AssessModel->startTestGetJSONData();
    $s2t2answers = new stdClass();
    $s2t2answers->{0} = new stdClass();
    $s2t2answers->{0}->{'uq'} = 0;
    $s2t2answers->{0}->{'ans'} = 'FALSE';
    $s2t2answers->{1} = new stdClass();
    $s2t2answers->{1}->{'uq'} = 1;
    $s2t2answers->{1}->{'ans'} = 'TRUE';
    $resultTwo = $this->_AssessModel->updateTestAnswers($s2t2answers);
    $this->assertTrue($resultOne && $resultTwo);

    // submit feedback for test
    $s2t2feedback = new stdClass();
    $s2t2feedback->{0} = 1;
    $s2t2feedback->{1} = 0;
    $this->assertTrue($this->_AssessModel->updateFeedbackFromStudent($s2t2feedback));
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
    print_r("\n  - end of VisualsModel Test -  \n\n");
  }
}
