<?php

/**
 *  AUTHORMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AuthorModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class and DB connection as instance variable
  private $_DB,
    $_AuthorModel,
    $_commonAuthorId;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_DB = DB::getInstance();
    $this->_AuthorModel = new AuthorModel();
    $this->_commonAuthorId = '247tnfn2303u54093nf3';
  }

  /**
   *  @test
   *  Create question that is compliant with question schema (boolean)
   */
  public function createQuestion_schemaCompliant_methodReturnsTrue() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'statement' => 'The capital city of France is Paris.',
      'singleAnswer' => 'TRUE',
      'feedbackCorrect' => 'It is indeed, well done.',
      'feedbackIncorrect' => 'Don\'t worry, geography wasn\'t my strongest subject either.'
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to create a question that does not belong to a recognised schema
   */
  public function createQuestion_unrecognisedSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion('inexistentSchema', array(
      'key' => 'value'
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Create question that does not provide optional data
   */
  public function createQuestion_booleanSchemaTestOptional_methodReturnsTrue() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'statement' => 'This statement is false. Seriously, not a trick question.',
      'singleAnswer' => 'FALSE'
    ));
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to create question that does not comply with question schema
   */
  public function createQuestion_doesNotComplyWithSchema_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'sandwich' => 'This is a sandwich, not a statement.',
      'filling' => 'Jam, of course.'
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question that does not supply all the required information
   */
  public function createQuestion_missingInformation_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'answer' => 'TRUE'
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question with inappropriate (invalid) data
   */
  public function createQuestion_invalidData_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'question' => 'Can I borrow your stapler?',
      'answer' => 'Certainly.'
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to create question with full valid data and extra invalid data
   */
  public function createQuestion_validQuestionPlusInvalidData_methodReturnsFalse() {

    $result = $this->_AuthorModel->createQuestion('boolean', array(
      'author' => $this->_commonAuthorId,
      'statement' => 'This question appears to be valid, but it is not.',
      'singleAnswer' => 'TRUE',
      'feedbackCorrect' => 'You\'ll see why soon',
      'feedbackIncorrect' => 'See extra junk data below...',
      'junk' => 'r93y02qhfgi3op2hg083qwghbn0o3w2'
    ));
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Get both questions created earlier matching the author id
   */
  public function getQuestions_matchingAuthorId_methodReturnsArrayOfTwoDocuments() {

    $documents = $this->_AuthorModel->getQuestions($this->_commonAuthorId);
    $this->assertEquals(2, count($documents));
  }

  /**
   *  @test
   *  Return an empty array for request for questions where author id doesn't match
   */
  public function getQuestions_authorIdDoesNotMatch_methodReturnsEmptyArray() {

    $result = $this->_AuthorModel->getQuestions('t4949984304903hnfgnfj3');
    $this->assertTrue(empty($result));
  }

  /**
   *  @test
   *  Attempt to get questions where the author id isn't hexadecimal characters
   */
  public function getQuestions_authorIdNotHexadecimalString_methodReturnsFalse() {

    $result = $this->_AuthorModel->getQuestions('<script>alert();</script>');
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Update an existing key value pair in a document that is not restricted
   */
  public function updateQuestion_performValidUpdate_methodReturnsTrue() {

    // get ID of the first question created
    $question = $this->_DB->read('questions', array(
      'statement' => 'The capital city of France is Paris.'
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array('singleAnswer' => 'FALSE')
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Update a question with a new key value pair (optional) that was not included on creation
   */
  public function updateQuestion_performUpdateNewOptionalKVPair_methodReturnsTrue() {

    // get ID of the first question created
    $question = $this->_DB->read('questions', array(
      'statement' => 'This statement is false. Seriously, not a trick question.'
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array('feedbackIncorrect' => 'Okay, it is a bit of a trick question. Sorry.')
    );
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Attempt to update a question with an invalid question identifier
   */
  public function updateQuestion_attemptNonMongoIdUpdate_methodReturnsFalse() {

    $result = $this->_AuthorModel->updateQuestion(
      'abc123def456',
      array('singleAnswer' => '9rt302hginowesngio')
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a question where the key does not exist in the schema
   */
  public function updateQuestion_attemptUpdateKeyNotInSchema_methodReturnsFalse() {

    $question = $this->_DB->read('questions', array(
      'statement' => 'The capital city of France is Paris.'
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array('custardSlice' => 'Custard slice or Vanilla slice, which is better?')
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Attempt to update a question where the update is not permitted (e.g. author update)
   */
  public function updateQuestion_attemptUpdateNotPermitted_methodReturnsFalse() {

    $question = $this->_DB->read('questions', array(
      'statement' => 'The capital city of France is Paris.'
    ));
    $questionId = key($question);

    $result = $this->_AuthorModel->updateQuestion(
      new MongoId($questionId),
      array('author' => 'hmitl3hmfol343943nfs')
    );
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Drop Questions collection (reset for later testing)
   */
  public function _dropQuestionsCollection_methodReturnsTrue() {

    $dropQuestionsResult = $this->_DB->delete('questions', 'DROP COLLECTION');
    $this->assertTrue($dropQuestionsResult);
  }
}
