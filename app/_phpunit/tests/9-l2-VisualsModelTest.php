<?php

/**
 *  VISUALSMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class VisualsModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variables
  private $_DB,
    $_UserModel,
    $_AuthorModel,
    $_AssessModel,
    $_VisualsModel;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_UserModel = new UserModel();
    $this->_AuthorModel = new AuthorModel();
    $this->_AssessModel = new AssessModel();
    $this->_VisualsModel = new VisualsModel();
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
      "name" => "JavaScript",
      "author" => $authorId,
      "question" => "JavaScript is also known as ECMAScript.",
      "singleAnswer" => "TRUE",
      "feedback" => "ECMAScript is the untrademarked name for the language."
    ));
    $resultTwo = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "D3",
      "author" => $authorId,
      "question" => "D3.js is not a data visualisation library.",
      "singleAnswer" => "FALSE",
      "feedback" => "D3.js is a data visualisation library created by Mike Bostock."
    ));
    $resultThree = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Angular JS",
      "author" => $authorId,
      "question" => "AngularJS is a front-end web application framework.",
      "singleAnswer" => "TRUE",
      "feedback" => "AngularJS is maintained by Google."
    ));
    $resultFour = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Open Source",
      "author" => $authorId,
      "question" => "Open-source software is the same as 'free' software.",
      "singleAnswer" => "FALSE",
      "feedback" => "Ask Richard Stallman for further details."
    ));
    $resultFive = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Unused",
      "author" => $authorId,
      "question" => "This question will not be included in any tests",
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
      "name" => "Test One",
      "author" => $authorId,
      "questions" => array(
        $questionsIds[0], $questionsIds[1]
      )
    ));
    $resultTwo = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "name" => "Test Two",
      "author" => $authorId,
      "questions" => array(
        $questionsIds[2], $questionsIds[3]
      )
    ));
    $resultThree = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "name" => "Test Not Taken",
      "author" => $authorId,
      "questions" => array(
        $questionsIds[1], $questionsIds[2]
      )
    ));
    $this->assertTrue($resultOne && $resultTwo && $resultThree);

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
  }

  /**
   *  @test 9.1
   *  Get list of question id's and names associated with a user account
   */
  public function getListOfQuestions_getAll_methodReturnsMatchingJSON() {

    // get author id and question ids
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $qOneDesc = "Unused";
    $qOneId = key($this->_DB->read("questions", array("name" => $qOneDesc)));
    $qTwoDesc = "JavaScript";
    $qTwoId = key($this->_DB->read("questions", array("name" => $qTwoDesc)));
    $qThreeDesc = "D3";
    $qThreeId = key($this->_DB->read("questions", array("name" => $qThreeDesc)));
    $qFourDesc = "Angular JS";
    $qFourId = key($this->_DB->read("questions", array("name" => $qFourDesc)));
    $qFiveDesc = "Open Source";
    $qFiveId = key($this->_DB->read("questions", array("name" => $qFiveDesc)));

    $this->assertSame(
      "{\"{$qTwoId}\":\"{$qTwoDesc}\",\"{$qThreeId}\":\"{$qThreeDesc}\",\"{$qFiveId}\":\"{$qFiveDesc}\"," .
      "\"{$qOneId}\":\"{$qOneDesc}\",\"{$qFourId}\":\"{$qFourDesc}\"}",
      $this->_VisualsModel->getListOfQuestions($authorId)
    );
  }

  /**
   *  @test 9.2
   *  Get single question data
   */
  public function getSingleQuestionJSON_getTakenData_methodReturnsMatchingJSON() {

    // get author, student and question ids
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $qId = key($this->_DB->read("questions", array("question" => "JavaScript is also known as ECMAScript.")));

    $this->assertSame(
      "{\"{$studentOne}\":{\"uq\":1,\"ca\":1,\"name\":\"Test Student\"}," .
        "\"{$studentTwo}\":{\"uq\":0,\"ca\":1,\"name\":\"Test Student Two\"}}",
      $this->_VisualsModel->getSingleQuestionJSON(new MongoId($qId), $authorId)
    );
  }

  /**
   *  @test 9.3
   *  Attempt to get question data where no "taken" information exists
   */
  public function getSingleQuestionJSON_noTakenData_methodReturnsFalse() {

    // get author id and question id for question that has not been taken
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $qId = key($this->_DB->read("questions", array("question" => "This question will not be included in any tests")));

    $this->assertFalse(
      $this->_VisualsModel->getSingleQuestionJSON(new MongoId($qId), $authorId)
    );
  }

  /**
   *  @test 9.4
   *  Attempt to get a question where the user id does not match the question author
   */
  public function getSingleQuestionJSON_authorDoesNotMatch_methodReturnsFalse() {

    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $qId = key($this->_DB->read("questions", array("question" => "JavaScript is also known as ECMAScript.")));

    $this->assertFalse(
      $this->_VisualsModel->getSingleQuestionJSON(new MongoId($qId), $studentTwo)
    );
  }

  /**
   *  @test 9.5
   *  Get list of test id's and names associated with a user account
   */
  public function getListOfTests_getAll_methodReturnsMatchingJSON() {

    // get author id and question ids
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $tNotTakenName = "Test Not Taken";
    $tNotTakenId = key($this->_DB->read("tests", array("name" => $tNotTakenName)));
    $tOneName = "Test One";
    $tOneId = key($this->_DB->read("tests", array("name" => $tOneName)));
    $tTwoName = "Test Two";
    $tTwoId = key($this->_DB->read("tests", array("name" => $tTwoName)));

    $this->assertSame(
      "{\"{$tNotTakenId}\":\"{$tNotTakenName}\",\"{$tOneId}\":\"{$tOneName}\",\"{$tTwoId}\":\"{$tTwoName}\"}",
      $this->_VisualsModel->getListOfTests($authorId)
    );
  }

  /**
   *  @test 9.6
   *  Get single test data
   */
  public function getSingleTestJSON_getTakenData_methodReturnsMatchingJSON() {

    // get author, student and test id
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentOne");
    $studentOne = $this->_UserModel->getUserData()->userId;
    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $tId = key($this->_DB->read("tests", array("name" => "Test Two")));

    $this->assertSame(
      "{\"testData\":{\"totalQuestions\":2}," .
        "\"userData\":{\"{$studentOne}\":{\"uq\":1,\"ca\":1,\"uf\":0,\"name\":\"Test Student\"}," .
        "\"{$studentTwo}\":{\"uq\":1,\"ca\":0,\"uf\":1,\"name\":\"Test Student Two\"}}}",
      $this->_VisualsModel->getSingleTestJSON(new MongoId($tId), $authorId)
    );
  }

  /**
   *  @test 9.7
   *  Attempt to get test data where no "taken" information exists
   */
  public function getSingleTestJSON_noTakenData_methodReturnsFalse() {

    // get author id and test id for test that has not been taken
    $this->_UserModel->findUser("testAuthor");
    $authorId = $this->_UserModel->getUserData()->userId;
    $tId = key($this->_DB->read("tests", array("name" => "Test Not Taken")));

    $this->assertFalse(
      $this->_VisualsModel->getSingleTestJSON(new MongoId($tId), $authorId)
    );
  }

  /**
   *  @test 9.8
   *  Attempt to get a test where the user id does not match the test author
   */
  public function getSingleTestJSON_authorDoesNotMatch_methodReturnsFalse() {

    $this->_UserModel->findUser("studentTwo");
    $studentTwo = $this->_UserModel->getUserData()->userId;
    $tId = key($this->_DB->read("tests", array("name" => "Test Two")));

    $this->assertFalse(
      $this->_VisualsModel->getSingleTestJSON(new MongoId($tId), $studentTwo)
    );
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
    print_r("\n  - end of VisualsModel Test -  \n");
  }
}
