/**
 *  ASSESS.JS
 *  @author Jonathan Lamb
 */

/**
 *  LOAD TEST
 *  Send a request to load a specific test
 */
function loadTest(testId) {

  $.ajax({
    url: baseURL + "assess/loadTest",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "html",
    success: function(response) {
      $('#selectTestPrompt').html('');
      $("#assessContainer").html(response);
    },
    error: function (request, status, error) {
      $("#assessContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  START TEST
 *  Send a confirmation to the server to start the test
 *  Server will return JSON of data on success
 */
function startTest() {

  $.ajax({
    url: baseURL + "assess/startTest",
    type: "GET",
    dataType: "json",
    success: function(response) {
      buildTest(response);
    },
    error: function (request, status, error) {
      $("#assessContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  BUILD TEST / UPDATE CONTAINER
 *  Use JSON data returned from server to build HTML for test taking
 */
var buildTest = function (data) {

  alert(data);
}

/**
 *  EXIT ASSESSMENT PLATFORM
 *  Return to the dashboard
 */
function exitPlatform() {

  window.location.replace(baseURL + "dashboard");
}
