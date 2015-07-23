<?php

class HelloTest extends PHPUnit_Framework_TestCase {

  /**
   *  @test
   */
  public function sayHello() {

    echo '   hello world';
    $this->assertEquals(1, 1);
  }
}
