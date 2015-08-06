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
        "controller" => "controller"
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
   *  Check that default templates are used in the absence of key/value pairs
   */
  public function renderTemplates_confirmDefaultTemplatesProvided_returnsArrayOfSpecificStrings() {

    $result = $this->_AppModel->renderTemplates("controller");
    $this->assertSame(
      array(
        "app/l4-ui/_headers/default.php",
        "app/l4-ui/controller/index.php",
        "app/l4-ui/_footers/default.php"
      ),
      $result
    );
  }

  /**
   *  @test
   *  Check if requested templates are used when passed as $views array values
   */
  public function renderTemplates_confirmRequestedTemplatesProvided_returnsArrayOfSpecificStrings() {

    $result = $this->_AppModel->renderTemplates("controller", array(
      "header" => "specific_header",
      "main" => "specific_main_template",
      "footer" => "specific_footer"
    ));
    $this->assertSame(
      array(
        "app/l4-ui/_headers/specific_header.php",
        "app/l4-ui/controller/specific_main_template.php",
        "app/l4-ui/_footers/specific_footer.php"
      ),
      $result
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
   */
  public function _confirmEnd() {
    print_r("\n  - end of AppModel Test -  \n\n");
  }
}
