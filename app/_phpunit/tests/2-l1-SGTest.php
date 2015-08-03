<?php

/**
 *  SGTEST.PHP
 *  @author Jonathan Lamb
 */
class SGTest extends PHPUnit_Framework_TestCase {

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
    $result = SG::get("test", "escape");
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
    $result = SG::get("url");
    $this->assertSame(
      array(
        "controller" => "home",
        "action" => "login",
        "parameters" => array(
          "param1", "param2", "param3"
        )
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
    $result = SG::get("test", "dangerous");
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

    $result = SG::get("test");
    $this->assertSame(
      "Unrecognised usage: please specify 'escape', 'url' (with key of 'url') or 'dangerous'",
      $result
    );
  }

  // POST TESTS

  /**
   *  @test
   *  Request escaped input (POST)
   */
  public function post_usageEqualsEscape_returnsEscapedInput() {

    $_POST["test"] = "<script>alert('hello');</script>";
    $result = SG::post("test", "escape");
    $this->assertSame(
      "&lt;script&gt;alert(&#039;hello&#039;);&lt;/script&gt;",
      $result
    );
  }

  /**
   *  @test
   *  Request dangerous input (POST)
   */
  public function post_usageEqualsDangerous_returnsUnsafeInput() {

    $_POST["test"] = "<script>alert('hello');</script>";
    $result = SG::post("test", "dangerous");
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

    $result = SG::post("test");
    $this->assertSame(
      "Unrecognised usage: please specify 'escape' or 'dangerous'",
      $result
    );
  }

  /**
   *  @test
   *  Check if empty session check returns false
   */
  public function session_checkInexistent_returnsFalse() {

    $result = SG::session("inexistent", "exists");
    $this->assertFalse($result);
  }

  /**
   *  @test
   *  Check if method for creating a new session returns true
   */
  public function session_putSessionValue_returnsTrue() {

    $result = SG::session("mySession", "put", "myValue");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check if existing session check returns true
   */
  public function session_checkExistingSession_returnsTrue() {

    SG::session("mySession2", "put", "myValue2");
    $result = SG::session("mySession2", "exists");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check if session 'get' method returns matching value
   */
  public function session_getSessionValue_returnsSpecificString() {

    SG::session("mySession3", "put", "myValue3");
    $result = SG::session("mySession3", "get");
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

    SG::session("mySession4", "put", "myValue4");
    $result = SG::session("mySession4", "delete");
    $this->assertTrue($result);
  }

  /**
   *  @test
   *  Check unspecified delete usage returns warning string
   */
  public function session_usageUnspecified_returnsSpecificString() {

    $result = SG::session("inexistent");
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
