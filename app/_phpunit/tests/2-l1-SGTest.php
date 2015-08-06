<?php

/**
 *  SGTEST.PHP
 *  @author Jonathan Lamb
 */
class SGTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_SG;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    $this->_SG = new SG();
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of SG Test -  \n");
  }

  /**
   *  @test
   *  Request escaped input (GET)
   */
  public function get_usageEqualsEscape_returnsEscapedInput() {

    $_GET["test"] = "<script>alert('hi');</script>";
    $result = $this->_SG->get("test", "escape");
    $this->assertSame(
      "&lt;script&gt;alert(&#039;hi&#039;);&lt;/script&gt;",
      $result
    );
  }

  /**
   *  @test
   *  Request url as array containing controller, action and parameters
   */
  public function get_usageEqualsUrl_returnsArray() {

    $_GET["url"] = "home/login/param1/param2/param3";
    $result = $this->_SG->get("url");
    $this->assertSame(
      array(
        "controller" => "home"
      ),
      $result
    );
  }

  /**
   *  @test
   *  Request dangerous input (GET)
   */
  public function get_usageEqualsDangerous_returnsUnsafeInput() {

    $_GET["test"] = "<script>alert('hi');</script>";
    $result = $this->_SG->get("test", "dangerous");
    $this->assertSame(
      "<script>alert('hi');</script>",
      $result
    );
  }

  /**
   *  @test
   *  Request with unspecified usage (key not 'url') (GET)
   */
  public function get_usageUnspecified_returnsSpecificString() {

    $result = $this->_SG->get("test");
    $this->assertSame(
      "Unrecognised usage: please specify 'escape', 'url' (with key of 'url') or 'dangerous'",
      $result
    );
  }

  /**
   *  @test
   *  Request escaped input (POST)
   */
  public function post_usageEqualsEscape_returnsEscapedInput() {

    $_POST["test"] = "<script>alert('hello');</script>";
    $result = $this->_SG->post("test", "escape");
    $this->assertSame(
      "&lt;script&gt;alert(&#039;hello&#039;);&lt;/script&gt;",
      $result
    );
  }

  /**
   *  @test
   *  Request parsed JSON input (POST)
   */
  public function post_usageEqualsJSON_returnsParsedInput() {

    $_POST["test"] = json_encode(array(
      "partOne" => array(
        "apples" => "great",
        "bananas" => "better",
        "custard" => "magnificent"
      ),
      "partTwo" => array(
        "toast" => "crispy",
        "jam" => "best in sandwiches"
      )
    ));
    $result = $this->_SG->post("test", "json");
    $this->assertObjectHasAttribute('bananas', $result->partOne);
  }

  /**
   *  @test
   *  Request JSON input that is not valid (POST)
   */
  public function post_usageEqualsJSONInvalidJSONInput_returnsJSONErrorMessage() {

    $_POST["test"] = "{'This is': 'invalid JSON'}";
    $result = $this->_SG->post("test", "json");
    $this->assertSame(
      "Invalid JSON: Syntax error",
      $result
    );
  }

  /**
   *  @test
   *  Request dangerous input (POST)
   */
  public function post_usageEqualsDangerous_returnsUnsafeInput() {

    $_POST["test"] = "<script>alert('hello');</script>";
    $result = $this->_SG->post("test", "dangerous");
    $this->assertSame(
      "<script>alert('hello');</script>",
      $result
    );
  }

  /**
   *  @test
   *  Request with unspecified usage (POST)
   */
  public function post_usageUnspecified_returnsSpecificString() {

    $result = $this->_SG->post("test");
    $this->assertSame(
      "Unrecognised usage: please specify 'escape', 'json' or 'dangerous'",
      $result
    );
  }

  /**
   *  @test
   *  Check if empty session check returns false
   */
  public function session_checkInexistent_returnsFalse() {

    $result = $this->_SG->session("inexistent", "exists");
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Check if method for creating a new session returns true
   */
  public function session_putSessionValue_returnsTrue() {

    $result = $this->_SG->session("mySession", "put", "myValue");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check if existing session check returns true
   */
  public function session_checkExistingSession_returnsTrue() {

    $this->_SG->session("mySession2", "put", "myValue2");
    $result = $this->_SG->session("mySession2", "exists");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check if session 'get' method returns matching value
   */
  public function session_getSessionValue_returnsSpecificString() {

    $this->_SG->session("mySession3", "put", "myValue3");
    $result = $this->_SG->session("mySession3", "get");
    $this->assertSame(
      "myValue3",
      $result
    );
  }

  /**
   *  @test
   *  Check if deleting a session returns true (boolean)
   */
  public function session_deleteSession_returnsTrue() {

    $this->_SG->session("mySession4", "put", "myValue4");
    $result = $this->_SG->session("mySession4", "delete");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check unspecified delete usage returns warning string
   */
  public function session_usageUnspecified_returnsSpecificString() {

    $result = $this->_SG->session("inexistent");
    $this->assertSame(
      "Unrecognised usage: please specify 'exists', 'put', 'get' or 'delete'",
      $result
    );
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of SG Test -  \n\n");
  }
}
