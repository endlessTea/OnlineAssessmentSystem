/**
 *  ASSESS.JS
 *  @author Jonathan Lamb
 */

/**
 *  LOAD TEST
 *  Send a request to load a specific test
 */
function checkAndLoadDisclaimer(testId) {

  $.ajax({
    url: baseURL + "assess/checkAndLoadDisclaimer",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "html",
    success: function(response) {
      $('#selectTestPrompt').html('');
      $("#assessContainer").html(response);
      if (response !== "Invalid test identifier" && response !== "The specified test is not available") {
        $("#assessContainer").append(
          "<button onclick=\"startTest('" +
          testId + "');\">START TEST</button>"
        );
      }
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
function startTest(testId) {

  $.ajax({
    url: baseURL + "assess/startTest",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "json",
    success: function(response) {
      $("#assessContainer").html('');
      buildTest(testId, response);
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
var buildTest = function (testId, data) {

  // create form element
  $("#assessContainer").html(
    "<form id=\"testForm\" onsubmit=\"submitAnswers('" +
    testId + "', '" + Object.keys(data).length + "'); return false;\"></form>"
  );

  // check the schema for each question and insert appropriate HTML with question values
  for (var question in data) {

    // title
    $("#testForm").append(
      "<h2>Question " + question + ":</h2>"
    );

    switch (data[question]["schema"]) {

      case "boolean":
        $("#testForm").append(
          "<h3>\"" + data[question]["statement"] + "\"</h3>" +
          "<p>Is this TRUE or FALSE?</p>" +
          "<input id=\"" + question + "-ans\" name=\"" + question + "-ans\"" +
            "type=\"radio\" value=\"TRUE\" checked> TRUE" +
          "<br>" +
          "<input id=\"" + question + "-ans\" name=\"" + question + "-ans\"" +
            "type=\"radio\" value=\"FALSE\"> FALSE"
        );
        break;

      default:
        alert("Question Schema not recognised. Please contact the system administrator.");
    }

    // obtain 'understanding of question' from the user
    $("#testForm").append(
      "<h4>Did you understand Question " + question + "?</h4>" +
      "<input id=\"" + question + "-uq\" name=\"" + question + "-uq\"" +
        "type=\"radio\" value=\"1\" checked> YES" +
      "<br>" +
      "<input id=\"" + question + "-uq\" name=\"" + question + "-uq\"" +
        "type=\"radio\" value=\"0\"> NO"
    );
  }

  // append submit button to test form
  $("#testForm").append(
    "<br><br><input type=\"submit\" value=\"SUBMIT\">"
  );
}

/**
 *  SUBMIT TEST ANSWERS
 *  Submit answers to the server
 */
function submitAnswers(testId, numQuestions) {



  alert(numQuestions);
}

/**
 *  EXIT ASSESSMENT PLATFORM
 *  Return to the dashboard
 */
function exitPlatform() {

  window.location.replace(baseURL + "dashboard");
}
