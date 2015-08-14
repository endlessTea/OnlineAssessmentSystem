<?php

/**
 *  DASHBOARDCONTROLLER.PHP
 *  Landing page for users once logged in
 *  Presents visualisation statistics to assessor accounts
 *  @author Jonathan Lamb
 */
class DashboardController {

  // instance variables
  private $_AppModel,
    $_UserModel,
    $_VisualsModel;

  /**
   *  Constructor
   *  Initialise App, User and Visualisation Models
   */
  public function __construct() {

    $this->_AppModel = new AppModel();
    $this->_UserModel = new UserModel();

    // initialise visualisation model only if assessor account is logged in
    if ($this->_UserModel->getUserData()->accountType === "assessor") {

      $this->_VisualsModel = new VisualsModel();

    } else {

      $this->_VisualsModel = "student";
    }
  }

  /**
   *  LOAD PAGE FRAME
   *  Load the HTML required to render the dashboard in the browser
   */
  public function loadFrame() {

    $resources["fullName"] = $this->_UserModel->getUserData()->fullName;
    $resources["accountType"] = $this->_UserModel->getUserData()->accountType;
    $this->_AppModel->renderFrame("Dashboard", $resources);
  }

  /**
   *  AJAX: GET ASSESSOR'S QUESTION LIST
   *  Return JSON representation of the questions the assessor has created
   */
  public function getAssessorsQuestionList() {

    if ($this->_UserModel->getUserData()->accountType !== "assessor") {

      echo "Insufficient account permissions (student account)";
      exit;
    }

    // change the header to indicate that JSON data is being returned
    header('Content-Type: application/json');

    echo $this->_VisualsModel->getListOfQuestions(
      $this->_UserModel->getUserData()->userId
    );
  }

  /**
   *  AJAX: GET QUESTION DATA
   *  Return JSON of question data if the user is the author of the question
   */
  public function getQuestionData() {

    if ($this->_UserModel->getUserData()->accountType !== "assessor") {

      echo "Insufficient account permissions (student account)";
      exit;
    }

    // attempt to convert test identifier to MongoId
    try {
      $questionIdObj = new MongoId($this->_AppModel->getPOSTData("qId"));
    } catch (Exception $e) {
      echo "Invalid test identifier.";
      exit;
    }

    $data = $this->_VisualsModel->getSingleQuestionJSON(
      $questionIdObj,
      $this->_UserModel->getUserData()->userId
    );
    if ($data === false) {
      echo "There was an issue loading the data.";
      exit;
    }

    // change the header to indicate that JSON data is being returned
    header('Content-Type: application/json');

    echo $data;
  }

  /**
   *  LOG USER OUT
   */
  public function logout() {

    $this->_UserModel->logUserOut();
    $this->_AppModel->redirectTo(URL);
  }
}
