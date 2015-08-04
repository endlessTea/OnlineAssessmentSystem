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
  public function createQuestion_schemaCompliant_methodReturnsTrue() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $this->_testQuestionAuthorId,
      "statement" => "The capital city of France is Paris.",
      "singleAnswer" => "TRUE",
      "feedbackCorrect" => "It is indeed, well done.",
      "feedbackIncorrect" => "Don't worry, it's an easy mistake to make."
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to create a question that does not belong to a recognised schema
   */
  public function createQuestion_unrecognisedSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "inexistentSchema",
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
      "author" => $this->_testQuestionAuthorId,
      "statement" => "This statement is false. Seriously, not a trick question.",
      "singleAnswer" => "FALSE"
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to create question that does not comply with question schema
   */
  public function createQuestion_doesNotComplyWithSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
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
      "author" => $this->_testQuestionAuthorId,
      "statement" => "This question appears to be valid, but it is not.",
      "singleAnswer" => "TRUE",
      "feedbackCorrect" => "You'll see why soon",
      "feedbackIncorrect" => "See extra junk data below...",
      "junk" => "r93y02qhfgi3op2hg083qwghbn0o3w2"
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get both questions created earlier matching the author id
   */
  public function getQuestions_matchingAuthorId_methodReturnsArrayOfTwoDocuments() {

    $documents = $this->_AuthorModel->getQuestions($this->_testQuestionAuthorId);
    $this->assertEquals(2, count($documents));
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
      "statement" => "The capital city of France is Paris."
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
      "statement" => "This statement is false. Seriously, not a trick question."
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array("feedbackIncorrect" => "Okay, it is a bit of a trick question. Sorry.")
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
      "statement" => "The capital city of France is Paris."
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
      "statement" => "The capital city of France is Paris."
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
      "statement" => "The capital city of France is Paris."
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
      "statement" => "This statement is false. Seriously, not a trick question."
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
      "author" => $this->_testTestAuthorId,
      "statement" => "2 + 2 = 4",
      "singleAnswer" => "TRUE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $this->_testTestAuthorId,
      "statement" => "2 + 4 = 10",
      "singleAnswer" => "FALSE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $this->_testTestAuthorId,
      "statement" => "5 * 5 = 25",
      "singleAnswer" => "TRUE",
    ));
    $this->_AuthorModel->createQuestion(array(
      "schema" => "boolean",
      "author" => $this->_testTestAuthorId,
      "statement" => "8 - 2 = 1",
      "singleAnswer" => "FALSE",
    ));

    // get questions & get id's
    $documents = $this->_AuthorModel->getQuestions($this->_testTestAuthorId);
    $questionIds = array_keys($documents);

    // create test
    $result = $this->_AuthorModel->createTest(array(
      "schema" => "standard",
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
      "author" => $this->_testTestAuthorId
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get test created earlier
   */
  public function getTests_checkTestReturnedContainsQuestionIds_arrayValuesMatch() {

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
    $userModel->createUser("jeeves", "password123");
    $user = $this->_DB->read("users", array(
      "username" => "jeeves"
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
      "username" => "jeeves"
    ));
    $studentId = new MongoId(key($user));

    // update the test document to reflect that the user has taken the test
    $this->_DB->update("tests", array("_id" => $testId), array("taken" => array(key($user))));
    $result = $this->_AuthorModel->makeTestAvailableToUser($testId, $studentId);
    $this->assertFalse($result);
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
    $this->assertTrue($dropQuestionsResult && $dropTestsResult && $dropUsersResult);
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of AuthorModel Test -  \n\n");
  }
}
