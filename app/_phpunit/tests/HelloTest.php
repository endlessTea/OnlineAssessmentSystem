<?php

class HelloTest extends PHPUnit_Framework_TestCase {

  /**
   *  @test
   */
  public function _sayHello_echoToTerminal_passTest() {

    echo '   hello world';
    $this->assertEquals(1, 1);
  }
}
