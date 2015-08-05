<?php

/**
 *  VISUALSMODEL.PHP
 *  Returns data required for visualisation of performance and question/feedback understanding
 *  @author Jonathan Lamb
 */
class VisualsModel {

  // store DB utility as instance variable
  private $_DB;

  /**
   *  Constructor
   *  Initialise instance variables
   */
  public function __construct() {

    // store instance of DB class for CRUD operations
    $this->_DB = DB::getInstance();
  }

  /*        VISUALISATION REQUIREMENTS
    // GET STUDENT PERFORMANCE, SINGLE QUESTION
    // GET STUDENT PERFORMANCE, SINGLE TEST
    // GET STUDENT PERFORMANCE, ALL TESTS - IDEA: Scatterplot, single colour
    // GET CLASS PERFORMANCE, SINGLE QUESTION
    // GET CLASS PERFORMANCE, SINGLE TEST - IDEA: Scatterplot, single colour
    // GET CLASS PERFORMANCE, ALL TESTS - IDEA: Scatterplot, multiple colours
    // GET FEEDBACK, SINGLE QUESTION
    // GET FEEDBACK, SINGLE TEST
    // GET FEEDBACK, ALL TESTS
  */
}
