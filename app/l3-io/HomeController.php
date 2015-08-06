<?php

/**
 *  HOMECONTROLLER.PHP
 *  Landing page for users once logged in
 *  @author Jonathan Lamb
 */
class HomeController {

  /**
   *  PAGE: index
   *  Default action of controller
   */
  public function index() {

    echo 'home controller, index method';
  }

  /**
   *  PAGE: fish
   */
  public function fish() {

    echo 'home controller, fish method *BLUB BLUB*';
  }
}
