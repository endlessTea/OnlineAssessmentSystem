/**
 *  ASSESS.JS
 *  @author Jonathan Lamb
 */

// Store current test ID and JSON representation of Questions and Feedback for sharing among methods
var testId;
var questionsJSON;
var feedbackJSON;

/**
 *  LOAD TEST
 *  Send a request to load a specific test
 */
function checkAndLoadDisclaimer(tIdArg) {

  $.ajax({
    url: baseURL + "assess/checkAndLoadDisclaimer",
    data: {
      tId: tIdArg
    },
    type: "POST",
    dataType: "html",
    success: function(response) {
      $('#selectTestPrompt').html('');
      $("#assessContainer").html(response);
      if (response !== "Invalid test identifier" && response !== "The specified test is not available") {

        // set 'testId' variable and assume disclaimer was provided; set start button
        testId = tIdArg;
        $("#assessContainer").append(
          "<button onclick=\"startTest();\">START TEST</button>"
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
function startTest() {

  $.ajax({
    url: baseURL + "assess/startTest",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "json",
    success: function(response) {
      $("#assessContainer").html('');
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
var buildTest = function(data) {

  // set variable to share JSON data among methods
  questionsJSON = data;

  // create form element
  $("#assessContainer").html(
    "<form id=\"testForm\" onsubmit=\"submitAnswers(); return false;\"></form>"
  );

  // check the schema for each question and insert appropriate HTML with question values
  for (var question in questionsJSON) {

    // title
    $("#testForm").append(
      "<h2>Question " + question + ":</h2>"
    );

    switch (questionsJSON[question]["schema"]) {

      case "boolean":
        $("#testForm").append(
          "<h3>\"" + questionsJSON[question]["statement"] + "\"</h3>" +
          "<p>Is this TRUE or FALSE?</p>" +
          "<input name=\"" + question + "-ans\"" +
          "type=\"radio\" value=\"TRUE\" checked> TRUE" +
          "<br>" +
          "<input name=\"" + question + "-ans\"" +
          "type=\"radio\" value=\"FALSE\"> FALSE"
        );
        break;

      default:
        alert("Question Schema not recognised. Please contact the system administrator.");
    }

    // obtain 'understanding of question' from the user
    $("#testForm").append(
      "<h4>Did you understand Question " + question + "?</h4>" +
      "<input name=\"" + question + "-uq\"" +
        "type=\"radio\" value=\"1\" checked> YES" +
      "<br>" +
      "<input name=\"" + question + "-uq\"" +
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
function submitAnswers() {

  // create root object
  var answers = {};

  for (var question in questionsJSON) {

    // create child object for each question
    answers[question] = {};

    // get 'understanding of question'
    answers[question]['uq'] = $('input[type="radio"][name="' + question + '-uq"]:checked').val();

    // check question schema to determine what values to retrieve
    switch (questionsJSON[question]["schema"]) {

      case "boolean":
        answers[question]['ans'] = $('input[type="radio"][name="' + question + '-ans"]:checked').val();
        break;

      default:
        alert("Question Schema not recognised. Please contact the system administrator.");
    }
  }

  // send answers via Ajax
  $.ajax({
    url: baseURL + "assess/submitAnswers",
    data: {
      tId: testId,
      ans: JSON.stringify(answers)
    },
    type: "POST",
    dataType: "json",
    success: function(response) {
      buildFeedbackResponse(response);
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
 *  BUILD FEEDBACK RESPONSE / UPDATE CONTAINER
 *  Use JSON data returned from server to build HTML for feedback delivery / student response
 */
var buildFeedbackResponse = function(data) {

  feedbackJSON = data;
  alert(JSON.stringify(feedbackJSON));

}

/**
 *  SUBMIT STUDENT FEEDBACK TO THE SERVER
 *  Submit feedback from the student to the server for each question incorrectly answered
 */
function submitFeedback() {

  // process form values

  // send feedback via Ajax

}

/**
 *  EXIT ASSESSMENT PLATFORM
 *  Return to the dashboard
 */
function exitPlatform() {

  window.location.replace(baseURL + "dashboard");
}
