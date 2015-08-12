<?php

/**
 *  APPMODELTEST.PHP
 *  @author Jonathan Lamb
 */
class AppModelTest extends PHPUnit_Framework_TestCase {

  // store instantiated class as instance variable
  private $_AppModel;

  /**
   *  Constructor
   *  Initialise instance variables (AppModel in test mode)
   */
  public function __construct() {

    $this->_AppModel = new AppModel("testMode");
  }

  /**
   *  @test
   */
  public function _confirmStart() {
    print_r(" - start of AppModel Test -  \n");
  }

  /**
   *  @test
   *  Check that a request for GET data returns escaped data
   */
  public function getGETData_confirmDataEscaped_returnsEscapedData() {

    $_GET["data"] = "<script>alert('here is data');</script>";
    $result = $this->_AppModel->getGETData("data");
    $this->assertSame(
      "&lt;script&gt;alert(&#039;here is data&#039;);&lt;/script&gt;",
      $result
    );
  }

  /**
   *  @test
   *  Check that the URL request returns a correctly parsed array of values
   */
  public function getURL_confirmValuesParsedAsArray_returnsArrayOfValues() {

    $_GET["url"] = "controller/action/param1/param2/param3/param4";
    $result = $this->_AppModel->getURL();
    $this->assertSame(
      array(
        "controller" => "controller",
        "action" => "action",
        "parameters" => array(
          "param1", "param2", "param3", "param4"
        )
      ),
      $result
    );
  }

  /**
   *  @test
   *  Check that a request for POST data returns escaped data
   */
  public function getPOSTData_confirmDataEscaped_returnsEscapedData() {

    $_POST["data"] = "<script>alert('post data');</script>";
    $result = $this->_AppModel->getPOSTData("data");
    $this->assertSame(
      "&lt;script&gt;alert(&#039;post data&#039;);&lt;/script&gt;",
      $result
    );
  }

  /**
   *  @test
   *  Check that the correct frames load for corresponding controllers
   */
  public function renderFrame_testAllControllers_methodsReturnMatchingStrings() {

    $this->assertSame(
      "app/l4-ui/Dashboard/Frame.php",
      $this->_AppModel->renderFrame("Dashboard")
    );
    $this->assertSame(
      "app/l4-ui/Author/Frame.php",
      $this->_AppModel->renderFrame("Author")
    );
    $this->assertSame(
      "app/l4-ui/Assess/Frame.php",
      $this->_AppModel->renderFrame("Assess")
    );
    $this->assertSame(
      "app/l4-ui/Error/Frame.php",
      $this->_AppModel->renderFrame("Error")
    );
  }

  /**
   *  @test
   *  Check if redirect will take user to 404 page with 404 parameter
   */
  public function redirectTo_testRedirectTo404ErrorPage_returnsSpecificString() {

    $result = $this->_AppModel->redirectTo(404);
    $this->assertSame(
      "Location: http://localhost/msc/error",
      $result
    );
  }

  /**
   *  @test
   *  Check if redirect will take user to 403 page with 403 parameter
   */
  public function redirectTo_testRedirectTo403ForbiddenPage_returnsSpecificString() {

    $result = $this->_AppModel->redirectTo(403);
    $this->assertSame(
      "Location: http://localhost/msc/forbidden",
      $result
    );
  }

  /**
   *  @test
   *  Check that the access method to return list of available schemas returns matching values
   */
  public function getSchemaList_requestValues_methodReturnsMatchingValues() {

    $this->assertSame(
      array("boolean", "multiple", "pattern"),
      $this->_AppModel->getSchemaList()
    );
  }

  /**
   *  @test
   */
  public function _confirmEnd() {
    print_r("\n  - end of AppModel Test -  \n\n");
  }
}
