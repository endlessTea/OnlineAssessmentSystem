<?php

/**
 *  AUTHORMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AuthorModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variable
  private $_DB,
    $_AuthorModel,
    $_testQuestionAuthorId,
    $_testTestAuthorId;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_AuthorModel = new AuthorModel();
    $this->_testQuestionAuthorId = "247tnfn2303u54093nf3";
    $this->_testTestAuthorId = "hgrn93y89543hgnrsdnbgo";
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of AuthorModel Test -  \n");
  }

  ##########################################################
  ################# 'QUESTIONS' UNIT TESTS #################
  ##########################################################

  /**
   *  @test
   *  Create question that is compliant with question schema (boolean)
   */
  public function createQuestion_schemaCompliantBoolean_methodReturnsTrue() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Capital of France",
      "author" => $this->_testQuestionAuthorId,
      "question" => "The capital city of France is Paris.",
      "singleAnswer" => "TRUE",
      "feedback" => "Don't worry, it's an easy mistake to make."
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Create schema compliant question (multiple choice)
   */
  public function createQuestion_schemaCompliantMultiple_methodReturnsTrue() {

    $this->assertTrue(
      $this->_AuthorModel->createQuestion(array(
        "schema" => "multiple",
        "name" => "Fruit select",
        "author" => $this->_testQuestionAuthorId,
        "question" => "Which of the following are fruits?",
        "options" => array(
          "Banana", "Custard", "Gooseberry", "Potato"
        ),
        "correctAnswers" => array(
          0, 2
        ),
        "feedback" => "RTFM (read the fruit manual)"
      ))
    );
  }

  /**
   *  @test
   *  Create schema compliant question (RegExp pattern)
   */
  public function createQuestion_schemaCompliantPattern_methodReturnsTrue() {

    $this->assertTrue(
      $this->_AuthorModel->createQuestion(array(
        "schema" => "pattern",
        "name" => "Name a fruit",
        "author" => $this->_testQuestionAuthorId,
        "question" => "Name a fruit beginning with 'A'",
        "pattern" => "/^[Aa]pple(s|z)?$/",
        "feedback" => "RTFM (read the fruit manual)"
      ))
    );
  }

  /**
   *  @test
   *  Create schema compliant question (short answer)
   */
  public function createQuestion_schemaCompliantShortAnswer_methodReturnsTrue() {

    $this->assertTrue(
      $this->_AuthorModel->createQuestion(array(
        "schema" => "short",
        "name" => "Describe JSON",
        "author" => $this->_testQuestionAuthorId,
        "question" => "What can JSON (JavaScript Object Notation) be used for?",
        "answer" => "JSON is a data format that allows key-value pairs to be exchanged via Ajax requests."
      ))
    );
  }

  /**
   *  @test
   *  Attempt to create a question that does not belong to a recognised schema
   */
  public function createQuestion_unrecognisedSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "inexistentSchema",
      "name" => "Where is this schema?",
      "key" => "value"
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Create question that does not provide optional data
   */
  public function createQuestion_booleanSchemaTestOptional_methodReturnsTrue() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Optional Feedback",
      "author" => $this->_testQuestionAuthorId,
      "question" => "This statement is false. Seriously, not a trick question.",
      "singleAnswer" => "FALSE"
    ));
    $this->assertTrue($result);
  }

  /*
   *  @test
   *  Attempt to create a short answer question where answer is not provided by the author
   */
  public function createQuestion_shortAnswerNoFeedback_methodReturnsTrue() {

    // this method should return false as feedback has been made required in the schema class
    $this->assertFalse(
      $this->_AuthorModel->createQuestion(array(
        "schema" => "short",
        "name" => "Where is the feedback",
        "author" => $this->_testQuestionAuthorId,
        "question" => "What can JSON (JavaScript Object Notation) be used for?"
      ))
    );
  }

  /**
   *  @test
   *  Attempt to create question that does not comply with question schema
   */
  public function createQuestion_doesNotComplyWithSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Sandwich madness",
      "author" => $this->_testQuestionAuthorId,
      "sandwich" => "This is a sandwich, not a statement.",
      "filling" => "Jam, of course."
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question that does not supply all the required information
   */
  public function createQuestion_missingInformation_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Something Missing",
      "author" => $this->_testQuestionAuthorId,
      "answer" => "TRUE"
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question with inappropriate (invalid) data
   */
  public function createQuestion_invalidData_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "This data is rubbish",
      "author" => $this->_testQuestionAuthorId,
      "question" => "Can I borrow your stapler?",
      "answer" => "Certainly."
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question with full valid data and extra invalid data
   */
  public function createQuestion_validQuestionPlusInvalidData_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Bonus Junk",
      "author" => $this->_testQuestionAuthorId,
      "question" => "This question appears to be valid, but it is not.",
      "singleAnswer" => "TRUE",
      "feedback" => "See extra junk data below...",
      "junk" => "r93y02qhfgi3op2hg083qwghbn0o3w2"
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get both questions created earlier matching the author id
   */
  public function getQuestions_matchingAuthorId_methodReturnsArrayOfFiveDocuments() {

    $documents = $this->_AuthorModel->getQuestions($this->_testQuestionAuthorId);
    $this->assertEquals(5, count($documents));
  }

  /**
   *  @test
   *  Return an empty array for request for questions where author id doesn't match
   */
  public function getQuestions_authorIdDoesNotMatch_methodReturnsEmptyArray() {

    $result = $this->_AuthorModel->getQuestions("t4949984304903hnfgnfj3");
    $this->assertTrue(empty($result));
  }

  /**
   *  @test
   *  Attempt to get questions where the author id isn't hexadecimal characters
   */
  public function getQuestions_authorIdNotHexadecimalString_methodReturnsFalse() {

    $result = $this->_AuthorModel->getQuestions("<script>alert();</script>");
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Update an existing key value pair in a document that is not restricted
   */
  public function updateQuestion_performValidUpdate_methodReturnsTrue() {

    // get ID of the first question created
    $question = $this->_DB->read("questions", array(
      "question" => "The capital city of France is Paris."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array("singleAnswer" => "FALSE")
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Update a question with a new key value pair (optional) that was not included on creation
   */
  public function updateQuestion_performUpdateNewOptionalKVPair_methodReturnsTrue() {

    // get ID of the first question created
    $question = $this->_DB->read("questions", array(
      "question" => "This statement is false. Seriously, not a trick question."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array("feedback" => "Okay, it is a bit of a trick question. Sorry.")
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to update a question with an invalid question identifier
   */
  public function updateQuestion_attemptNonMongoIdUpdate_methodReturnsFalse() {

    $result = $this->_AuthorModel->updateQuestion(
      "abc123def456",
      array("singleAnswer" => "9rt302hginowesngio")
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a question where the key does not exist in the schema
   */
  public function updateQuestion_attemptUpdateKeyNotInSchema_methodReturnsFalse() {

    $question = $this->_DB->read("questions", array(
      "question" => "The capital city of France is Paris."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array("custardSlice" => "Custard slice or Vanilla slice, which is better?")
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a question where the update is not permitted (e.g. author update)
   */
  public function updateQuestion_attemptUpdateNotPermitted_methodReturnsFalse() {

    $question = $this->_DB->read("questions", array(
      "question" => "The capital city of France is Paris."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array("author" => "hmitl3hmfol343943nfs")
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Delete a question with matching author ID
   */
  public function deleteQuestion_deleteWithMatchingAuthor_methodReturnsTrue() {

    // obtain MongoId value for first question
    $question = $this->_DB->read("questions", array(
      "question" => "The capital city of France is Paris."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->deleteQuestion(
      new MongoId($questionId),
      $this->_testQuestionAuthorId
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to delete a question with an ID that isn't a Mongo ID object
   */
  public function deleteQuestion_attemptDeleteNotMongoId_methodReturnsFalse() {

    $result = $this->_AuthorModel->deleteQuestion(
      "abc123def456",
      $this->_testQuestionAuthorId
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to delete a question with an author ID that doesn't match
   */
  public function deleteQuestion_attemptDeleteAuthorIdDoesntMatch_methodReturnsFalse() {

    // get last question's id
    $question = $this->_DB->read("questions", array(
      "question" => "This statement is false. Seriously, not a trick question."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->deleteQuestion(
      new MongoId($questionId),
      "hmitl3hmfol343943nfs"
    );
    $this->assertFalse($result);
  }

  #####################################################
  ################# 'TEST' UNIT TESTS #################
  #####################################################

  /**
   *  @test
   *  Create questions and create a typical test
   */
  public function createTest_createQuestionsTypicalTest_methodReturnsTrue() {

    // create questions
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Maths 1",
      "author" => $this->_testTestAuthorId,
      "question" => "2 + 2 = 4",
      "singleAnswer" => "TRUE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Maths 2",
      "author" => $this->_testTestAuthorId,
      "question" => "2 + 4 = 10",
      "singleAnswer" => "FALSE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Maths 3",
      "author" => $this->_testTestAuthorId,
      "question" => "5 * 5 = 25",
      "singleAnswer" => "TRUE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "name" => "Maths 4",
      "author" => $this->_testTestAuthorId,
      "question" => "8 - 2 = 1",
      "singleAnswer" => "FALSE",
    ));

    // get questions & get id's
    $documents = $this->_AuthorModel->getQuestions($this->_testTestAuthorId);
    $questionIds = array_keys($documents);

    // create test
    $result = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "name" => "Sample Test",
      "author" => $this->_testTestAuthorId,
      "questions" => $questionIds
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to create a test with a missing requirement (questions)
   */
  public function createTest_attemptToCreateMissingRequirement_methodReturnsFalse() {

    $result = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
      "name" => "Bad Sample",
      "author" => $this->_testTestAuthorId
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get test created earlier
   */
  public function getTests_validRequest_methodReturnsTest() {

    $document = $this->_AuthorModel->getTests($this->_testTestAuthorId);
    $this->assertEquals(1, count($document));
  }

  /**
   *  @test
   *  Return an empty array for request for tests where author id doesn't match
   */
  public function getTests_authorIdDoesNotMatch_methodReturnsEmptyArray() {

    $result = $this->_AuthorModel->getTests("t4949984304903hnfgnfj3");
    $this->assertTrue(empty($result));
  }

  /**
   *  @test
   *  Attempt to get tests where the author id isn't hexadecimal characters
   */
  public function getTests_authorIdNotHexadecimalString_methodReturnsFalse() {

    $result = $this->_AuthorModel->getTests("<script>alert();</script>");
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Request a single test, providing test id and author id
   */
  public function getSingleTest_validRequest_methodReturnsTest() {

    // get test id
    $testId = key($this->_DB->read("tests", array("author" => $this->_testTestAuthorId)));

    $this->assertEquals(
      1,
      count($this->_AuthorModel->getSingleTest(new MongoId($testId), $this->_testTestAuthorId))
    );
  }

  /**
   *  @test
   *  Return false for test id that is not a MongoId object
   */
  public function getSingleTest_testIdNotMongoIdObject_methodReturnsFalse() {

    $this->assertFalse($this->_AuthorModel->getSingleTest(
      "nfie39h9f032nf3p2nf3f3f",
      $this->_testTestAuthorId
    ));
  }

  /**
   *  @test
   *  Attempt to get tests where the author id isn't hexadecimal characters
   */
  public function getSingleTest_authorIdNotHexadecimalString_methodReturnsFalse() {

    // get test id
    $testId = key($this->_DB->read("tests", array("author" => $this->_testTestAuthorId)));

    $this->assertFalse($this->_AuthorModel->getSingleTest(
      new MongoId($testId),
      "<script>alert();</script>"
    ));
  }

  /**
   *  @test
   *  Check details for a test that has not been taken or issued
   */
  public function getFullTestDetails_testNotIssuedOrTaken_methodReturnsMatchingJSON() {

    $testId = key($this->_DB->read("tests", array("author" => $this->_testTestAuthorId)));

    $qOneDesc = "2 + 2 = 4";
    $qOneId = key($this->_DB->read("questions", array("question" => $qOneDesc)));
    $qTwoDesc = "2 + 4 = 10";
    $qTwoId = key($this->_DB->read("questions", array("question" => $qTwoDesc)));
    $qThreeDesc = "5 * 5 = 25";
    $qThreeId = key($this->_DB->read("questions", array("question" => $qThreeDesc)));
    $qFourDesc = "8 - 2 = 1";
    $qFourId = key($this->_DB->read("questions", array("question" => $qFourDesc)));

    $this->assertSame(
      "{\"questions\":{\"{$qOneId}\":{\"name\":\"Maths 1\",\"type\":\"Boolean\",\"question\":\"{$qOneDesc}\"}," .
        "\"{$qTwoId}\":{\"name\":\"Maths 2\",\"type\":\"Boolean\",\"question\":\"{$qTwoDesc}\"}," .
        "\"{$qThreeId}\":{\"name\":\"Maths 3\",\"type\":\"Boolean\",\"question\":\"{$qThreeDesc}\"}," .
        "\"{$qFourId}\":{\"name\":\"Maths 4\",\"type\":\"Boolean\",\"question\":\"{$qFourDesc}\"}}}",
      $this->_AuthorModel->getFullTestDetails(new MongoId($testId), $this->_testTestAuthorId)
    );
  }

  /**
   *  @test
   *  Check details for a test that has been taken and issued
   */
  public function getFullTestDetails_testTaken_methodReturnsMatchingJSON() {

    // create users to take test and get id's
    $um = new UserModel();
    $um->createUser("stdone", "password", "Student One");
    $um->findUser("stdone");
    $stdOneId = $um->getUserData()->userId;
    $stdOneName = $um->getUserData()->fullName;
    $um->createUser("stdtwo", "password", "Student Two");
    $um->findUser("stdtwo");
    $stdTwoId = $um->getUserData()->userId;
    $stdTwoName = $um->getUserData()->fullName;

    $testId = key($this->_DB->read("tests", array("author" => $this->_testTestAuthorId)));

    // add users to available/taken arrays
    $this->assertTrue($this->_DB->update(
      "tests",
      array("_id" => new MongoId($testId)),
      array("available" => array($stdOneId)
    )));
    $this->assertTrue($this->_DB->update(
      "tests",
      array("_id" => new MongoId($testId)),
      array("taken" => array($stdTwoId => "4")
    )));

    $qOneDesc = "2 + 2 = 4";
    $qOneId = key($this->_DB->read("questions", array("question" => $qOneDesc)));
    $qTwoDesc = "2 + 4 = 10";
    $qTwoId = key($this->_DB->read("questions", array("question" => $qTwoDesc)));
    $qThreeDesc = "5 * 5 = 25";
    $qThreeId = key($this->_DB->read("questions", array("question" => $qThreeDesc)));
    $qFourDesc = "8 - 2 = 1";
    $qFourId = key($this->_DB->read("questions", array("question" => $qFourDesc)));

    $this->assertSame(
      "{\"questions\":{\"{$qOneId}\":{\"name\":\"Maths 1\",\"type\":\"Boolean\",\"question\":\"{$qOneDesc}\"}," .
        "\"{$qTwoId}\":{\"name\":\"Maths 2\",\"type\":\"Boolean\",\"question\":\"{$qTwoDesc}\"}," .
        "\"{$qThreeId}\":{\"name\":\"Maths 3\",\"type\":\"Boolean\",\"question\":\"{$qThreeDesc}\"}," .
        "\"{$qFourId}\":{\"name\":\"Maths 4\",\"type\":\"Boolean\",\"question\":\"{$qFourDesc}\"}}," .
        "\"issued\":{\"{$stdOneId}\":\"{$stdOneName}\"}," .
        "\"taken\":{\"{$stdTwoId}\":\"{$stdTwoName}\"}}",
      $this->_AuthorModel->getFullTestDetails(new MongoId($testId), $this->_testTestAuthorId)
    );
  }

  /**
   *  @test
   *  Attempt to get test details when the user is not the author of the test
   */
  public function getFullTestDetails_userIsNotAuthor_methodReturnsFalse() {

    // attempt to get details with a student id
    $um = new UserModel();
    $um->findUser("stdone");
    $stdOneId = $um->getUserData()->userId;
    $testId = key($this->_DB->read("tests", array("author" => $this->_testTestAuthorId)));

    $this->assertFalse(
      $this->_AuthorModel->getFullTestDetails(new MongoId($testId), $stdOneId)
    );
  }

  /**
   *  @test
   *  Update an existing key value pair in a test that is not restricted
   */
  public function updateTest_performValidUpdate_methodReturnsTrue() {

    // get ID of the test created
    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->updateTest(
      new MongoId($testId),
      array("questions" => array(
        "fng84902hnbtf4892hf42o",
        "8903hngfoensiofgn980w2"
      ))
    );
    $this->assertTrue($result);
  }

  // TODO: reject update if questions is not an array

  /**
   *  @test
   *  Attempt to update a test if questions are not an array
   */
  public function updateTest_attemptUpdateQuestionsNotArray_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->updateTest(
      new MongoId($testId),
      array(
        "questions" => "not an array"
      )
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a test with an invalid identifier (i.e. not MongoId)
   */
  public function updateTest_attemptNonMongoIdUpdate_methodReturnsFalse() {

    $result = $this->_AuthorModel->updateTest(
      "abc123def456",
      array("questions" => array(
        "fng84902hnbtf4892hf42o",
        "8903hngfoensiofgn980w2"
      ))
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a test where the key does not exist in the schema
   */
  public function updateTest_attemptUpdateKeyNotInSchema_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->updateTest(
      new MongoId($testId),
      array(
        "badKey" => "worseValue"
      )
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a test where the update is not permitted (e.g. author update)
   */
  public function updateTest_attemptUpdateNotPermitted_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->updateTest(
      new MongoId($testId),
      array("author" => "hmitl3hmfol343943nfs")
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get a valid list of groups and students that may take a test
   */
  public function getStudentsForTest_checkGroupAndStudentsReturn_methodReturnsMatchingJSON() {

    // create users and group
    $um = new UserModel();
    $um->createUser("stdthree", "password", "Student Three");
    $um->findUser("stdthree");
    $stdThreeId = $um->getUserData()->userId;
    $um->createUser("stdfour", "password", "Student Four");
    $um->findUser("stdfour");
    $stdFourId = $um->getUserData()->userId;
    $um->createUser("stdfive", "password", "Student Five");
    $um->findUser("stdfive");
    $stdFiveId = $um->getUserData()->userId;

    $studentArray = array($stdThreeId, $stdFourId);
    $this->assertTrue($um->createGroup("Test Group", $studentArray));
    $groupId = key($this->_DB->read("groups", array("name" => "Test Group")));

    // now create another group with a student that has taken/been issued the test
    $um->findUser("stdone");
    $stdOneId = $um->getUserData()->userId;
    $studentArray[] = $stdOneId;
    $this->assertTrue($um->createGroup("Not Applicable Group", $studentArray));

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $this->assertSame(
      "{\"groups\":{\"{$groupId}\":\"Test Group\"}," .
        "\"students\":{\"{$stdThreeId}\":\"Student Three\"," .
        "\"{$stdFourId}\":\"Student Four\",\"{$stdFiveId}\":\"Student Five\"}}",
      $this->_AuthorModel->getStudentsForTest(
        new MongoId($testId),
        $this->_testTestAuthorId
      )
    );
  }

  /**
   *  @test
   *  Make test available to a valid user
   */
  public function makeTestAvailableToUser_validUserAssociation_methodReturnsTrueAndIdRegistered() {

    // get test created earlier & turn into MongoId
    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = new MongoId(key($test));

    // create a user & turn into MongoId
    $userModel = new UserModel();
    $userModel->createUser("jeeves", "password123", "Jeeves");
    $user = $this->_DB->read("users", array(
      "user_name" => "jeeves"
    ));
    $studentId = new MongoId(key($user));

    $result = $this->_AuthorModel->makeTestAvailableToUser($testId, $studentId);
    $this->assertTrue($result);

    // get updated test
    $test = array_pop($this->_DB->read("tests", array(
      "_id" => $testId
    )));
    $this->assertTrue(in_array(key($user), $test["available"]));
  }

  /**
   *  @test
   *  Attempt to make test available to invalid user id
   */
  public function makeTestAvailableToUser_invalidUserId_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = new MongoId(key($test));

    $result = $this->_AuthorModel->makeTestAvailableToUser($testId, "rfu9320fn3o2wno");
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to make test available to a user that has already taken the test
   */
  public function makeTestAvailableToUser_userAlreadyTakenTest_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = new MongoId(key($test));

    // get user that would have already taken the test
    $user = $this->_DB->read("users", array(
      "user_name" => "jeeves"
    ));
    $studentId = new MongoId(key($user));

    // update the test document to reflect that the user has taken the test
    $this->_DB->update("tests", array("_id" => $testId), array("taken" => array(key($user))));
    $result = $this->_AuthorModel->makeTestAvailableToUser($testId, $studentId);
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Make test available to a group where no members have taken it / been issued it
   */
  public function makeTestAvailableToGroup_validGroupRequest_methodReturnsTrue() {

    // create users
    $um = new UserModel();
    $um->createUser("groupmem1", "password", "Group Member One");
    $um->findUser("groupmem1");
    $gmOne = $um->getUserData()->userId;
    $um->createUser("groupmem2", "password", "Group Member Two");
    $um->findUser("groupmem2");
    $gmTwo = $um->getUserData()->userId;
    $um->createUser("groupmem3", "password", "Group Member Three");
    $um->findUser("groupmem3");
    $gmThree = $um->getUserData()->userId;

    // create group
    $ids = array($gmOne, $gmTwo, $gmThree);
    $um->createGroup("Test Group One", $ids);
    $group = $this->_DB->read("groups", array("name" => "Test Group One"));
    $gId = key($group);

    // get test id
    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $this->assertTrue(
      $this->_AuthorModel->makeTestAvailableToGroup(
        new MongoId($testId),
        new MongoId($gId)
      )
    );
  }

  /**
   *  @test
   *  Attempt to issue test to a group where one of the members has already taken the test
   */
  public function makeTestAvailableToGroup_memberAlreadyTaken_methodReturnsFalse() {

    // create users
    $um = new UserModel();
    $um->createUser("groupmem4", "password", "Group Member Four");
    $um->findUser("groupmem4");
    $gmFour = $um->getUserData()->userId;
    $user = $this->_DB->read("users", array(
      "user_name" => "jeeves"
    ));
    $studentTakenId = key($user);

    // create group
    $ids = array($gmFour, $studentTakenId);
    $um->createGroup("Test Group Two", $ids);
    $group = $this->_DB->read("groups", array("name" => "Test Group Two"));
    $gId = key($group);

    // get test id
    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $this->assertFalse(
      $this->_AuthorModel->makeTestAvailableToGroup(
        new MongoId($testId),
        new MongoId($gId)
      )
    );
  }

  /**
   *  @test
   *  Attempt to delete a test with an author ID that doesn't match
   */
  public function deleteTest_attemptDeleteAuthorIdDoesntMatch_methodReturnsFalse() {

    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->deleteTest(
      new MongoId($testId),
      "hmitl3hmfol343943nfs"
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to delete a test with an ID that isn't a Mongo ID object
   */
  public function deleteTest_attemptDeleteNotMongoId_methodReturnsFalse() {

    $result = $this->_AuthorModel->deleteTest(
      "abc123def456",
      $this->_testTestAuthorId
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Delete a test with matching author ID
   */
  public function deleteTest_deleteWithMatchingAuthor_methodReturnsTrue() {

    // obtain MongoId value for first test
    $test = $this->_DB->read("tests", array(
      "author" => $this->_testTestAuthorId
    ));
    $testId = key($test);

    $result = $this->_AuthorModel->deleteTest(
      new MongoId($testId),
      $this->_testTestAuthorId
    );
    $this->assertTrue($result);
  }

  #########################################
  ################# RESET #################
  #########################################

  /**
   *  @test
   *  Drop Questions, Tests and Users collections (reset for later testing)
   */
  public function _dropCollections_methodsReturnsTrue() {

    $dropQuestionsResult = $this->_DB->delete("questions", "DROP COLLECTION");
    $dropTestsResult = $this->_DB->delete("tests", "DROP COLLECTION");
    $dropUsersResult = $this->_DB->delete("users", "DROP COLLECTION");
    $dropGroupsResult = $this->_DB->delete("groups", "DROP COLLECTION");
    $this->assertTrue($dropQuestionsResult && $dropTestsResult && $dropUsersResult && $dropGroupsResult);
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of AuthorModel Test -  \n\n");
  }
}
