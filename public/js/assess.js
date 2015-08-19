/**
 *  ASSESS.JS
 *  @author Jonathan Lamb
 */

// Store current test ID and JSON representation of Questions and Feedback for sharing among methods
var testId;
var questionsJSON;
var feedbackJSON;
var selfAssessJSON;
var answersJSON;
var selfAssess = false;

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

  // set variable to share JSON data among methods and reset self-assessment flag
  questionsJSON = data;
  selfAssess = false;

  // create form element
  $("#assessContainer").html(
    "<form id=\"testForm\" onsubmit=\"storeFormAnswers(); return false;\"></form>"
  );

  // check the schema for each question and insert appropriate HTML with question values
  for (var question in questionsJSON) {

    // append a container for each question
    $('#testForm').append(
      "<div id=\"q-" + question + "-container\" class=\"question-container\"></div>"
    )

    // title: present question "0" as question "1" etc.
    $("#q-" + question + "-container").append(
      "<h2>Question " + (parseInt(question) + 1) + ":</h2>"
    );

    switch (questionsJSON[question]["schema"]) {

      case "boolean":
        $("#q-" + question + "-container").append(
          "<h3>\"" + questionsJSON[question]["question"] + "\"</h3>" +
          "<p>Is this TRUE or FALSE?</p>" +
          "<input name=\"" + question + "-ans\"" +
          "type=\"radio\" value=\"TRUE\" checked> TRUE" +
          "<br>" +
          "<input name=\"" + question + "-ans\"" +
          "type=\"radio\" value=\"FALSE\"> FALSE"
        );
        break;

      case "multiple":

        // create container
        $("#q-" + question + "-container").append(
          "<h3>" + questionsJSON[question]["question"] + "</h3>" +
          "<p>Select the correct answers from the options below:</p>" +
          "<div id=\"" + question + "-chk-con\"></div>"
        );

        // create options
        var options = questionsJSON[question]["options"];
        for (var i = 0; i < options.length; i++) {
          $('#' + question + '-chk-con').append(
            "<p>" + options[i] +
            "&nbsp;<input type=\"checkbox\" name=\"" + i + "\"></p>"
          );
        }
        break;

      case "pattern":
        $("#q-" + question + "-container").append(
          "<h3>\"" + questionsJSON[question]["question"] + "\"</h3>" +
          "<p>Type a single word answer in the box below:</p>" +
          "<input id=\"" + question + "-rx\"" +
          "type=\"text\" required autocomplete=\"off\">"
        );
        break;

      case "short":
        $("#q-" + question + "-container").append(
          "<h3>\"" + questionsJSON[question]["question"] + "\"</h3>" +
          "<p>Type a short answer in the box below:</p>" +
          "<input id=\"" + question + "-ans\"" +
          "type=\"text\" required autocomplete=\"off\">"
        );
        break;

      default:
        alert("Question Schema not recognised. Please contact the system administrator.");
        return;
    }

    // obtain 'understanding of question' from the user
    $("#q-" + question + "-container").append(
      "<h4>Did you understand Question " + (parseInt(question) + 1) + "?</h4>" +
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
 *  STORE ANSWERS SO FAR (PREPARE FOR SELF-MARKING QUESTIONS)
 *  Store answers so far as a variable.
 *  If self-assessment is required, call self-assessment method, otherwise submit answers.
 */
function storeFormAnswers() {

  answersJSON = {};

  for (var question in questionsJSON) {

    // create child object for each question
    answersJSON[question] = {};

    // get 'understanding of question'
    answersJSON[question]['uq'] = $('input[type="radio"][name="' + question + '-uq"]:checked').val();

    // check question schema to determine what values to retrieve
    switch (questionsJSON[question]["schema"]) {

      case "boolean":
        answersJSON[question]['ans'] = $('input[type="radio"][name="' + question + '-ans"]:checked').val();
        break;

      case "multiple":
        answersJSON[question]['ans'] = [];
        $('#' + question + '-chk-con input:checked').each(function() {
          answersJSON[question]['ans'].push($(this).attr('name'));
        });
        break;

      case "pattern":
        answersJSON[question]['ans'] = $('#' + question + '-rx').val();
        break;

      // mark test as requiring self assessment
      case "short":
        if (!selfAssess) selfAssess = true;
        answersJSON[question]['ans'] = $('#' + question + '-ans').val();
        break;

      default:
        alert("Question Schema not recognised. Please contact the system administrator.");
        return;
    }
  }

  if (selfAssess) getSelfMarkingAnswers();
  else submitAnswers();
}

/**
 *  GET ANSWERS FOR SELF-MARKING QUESTIONS
 *  If self-assessment is required, fetch answers and build form
 */
var getSelfMarkingAnswers= function() {

  $.ajax({
    url: baseURL + "assess/getSelfMarkingAnswers",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "json",
    success: function(response) {
      selfAssessJSON = response;
      buildSelfAssessmentForm(response);
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
 *  BUILD SELF-ASSESSMENT FORM
 *  Construct HTML form to allow user to mark their own short answer questions
 */
var buildSelfAssessmentForm = function(data) {

  // advise user they need to mark their own short answers (create form element)
  $("#assessContainer").html(
    "<h2>Self-marking required to continue</h2>" +
    "<p>Please mark your answers as correct or incorrect according " +
    "to the example answers provided by the assessor:</p>" +
    "<form id=\"selfAnsForm\" onsubmit=\"processSelfAssessmentForm(); return false;\"></form>"
  );

  for (var answer in data) {

    // append a container for each question
    $('#selfAnsForm').append(
      "<div id=\"q-" + answer + "-container\" class=\"question-container\"></div>"
    )

    $("#q-" + answer + "-container").append(
      "<h3>Question " + (parseInt(answer) + 1) + ":</h3>" +
      "<p>The example answer from the assessor is:</p>" +
      "<h4>" + data[answer]["ans"] + "</h4>" +
      "<p>The answer that you provided was:</p>" +
      "<h4>" + answersJSON[answer]["ans"] + "</h4>" +
      "<p>Please mark your answer as 'Correct' or 'Incorrect'</p>" +
      "<input name=\"" + answer + "-mark\"" +
      "type=\"radio\" value=\"1\" checked> CORRECT" +
      "<br>" +
      "<input name=\"" + answer + "-mark\"" +
      "type=\"radio\" value=\"0\"> INCORRECT"
    );
  }

  // append submit button
  $("#selfAnsForm").append(
    "<br><br><input type=\"submit\" value=\"SUBMIT\">"
  );
}

/**
 *  PROCESS SELF-ASSESSMENT FORM AND UPDATE / SUBMIT ANSWERS
 *  Store user's marks and unset short answer answers. Submit answers to server.
 */
var processSelfAssessmentForm = function() {

  for (var answer in selfAssessJSON) {

    // remove respective answer from answersJSON
    delete answersJSON[answer]["ans"];

    // add indication of correct answer
    answersJSON[answer]["ca"] = $('input[type="radio"][name="' + answer + '-mark"]:checked').val();
  }

  submitAnswers();
}

/**
 *  SUBMIT TEST ANSWERS
 *  Submit answers to the server
 */
var submitAnswers = function() {

  // send answers via Ajax
  $.ajax({
    url: baseURL + "assess/submitAnswers",
    data: {
      tId: testId,
      ans: JSON.stringify(answersJSON)
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

  // inform user of their score
  $("#assessContainer").html(
    "<h2>Results</h2>" +
    "<p>Your score is:</p>" +
    "<h3>" + feedbackJSON.score + " / " + Object.keys(questionsJSON).length + "</h3>"
  );

  // check if feedback is available
  if ($.isEmptyObject(feedbackJSON.feedback)) {

    $("#assessContainer").append(
      "<h2>Congratulations!</h2>" +
      "<p>You achieved full marks: there is no further feedback from the assessor.<br>" +
      "Please click 'Exit' to leave the assessment platform.</p>"
    );

  } else {

    $("#assessContainer").append(
      "<h2>Feedback</h2>" +
      "<p>The assessor provided the following feedback for questions guessed incorrectly.<br>" +
      "Please review this feedback and provide an indication of its usefulness to you:</p>"
    );

    // create form element
    $("#assessContainer").append(
      "<form id=\"feedForm\" onsubmit=\"submitFeedback(); return false;\"></form>"
    );

    for (var item in feedbackJSON.feedback) {

      // append a container for each question
      $('#feedForm').append(
        "<div id=\"q-" + item + "-container\" class=\"question-container\"></div>"
      )

      $("#q-" + item + "-container").append(
        "<h3>Question " + (parseInt(item) + 1) + ":</h3>" +
        "<h4>" + feedbackJSON.feedback[item] + "</h4>" +
        "<p>Do you understand the feedback?</p>" +
        "<input name=\"" + item + "-uf\"" +
          "type=\"radio\" value=\"1\" checked> YES" +
        "<br>" +
        "<input name=\"" + item + "-uf\"" +
          "type=\"radio\" value=\"0\"> NO"
      );
    }

    // append form submission button
    $("#feedForm").append(
      "<br><br><input type=\"submit\" value=\"SUBMIT\">"
    );
  }
}

/**
 *  SUBMIT STUDENT FEEDBACK TO THE SERVER
 *  Submit feedback from the student to the server for each question incorrectly answered
 */
function submitFeedback() {

  // process form values: create root object
  var feedback = {};

  for (var item in feedbackJSON.feedback) {

    feedback[item] = $('input[type="radio"][name="' + item + '-uf"]:checked').val();
  }

  // send feedback via Ajax
  $.ajax({
    url: baseURL + "assess/submitFeedback",
    data: {
      tId: testId,
      feed: JSON.stringify(feedback)
    },
    type: "POST",
    dataType: "html",
    success: function(response) {
      if (response === "ok") {
        $("#assessContainer").html(
          "<h2>Thanks!</h2>" +
          "<p>Your feedback has been received and processed by the application.<br>" +
          "Please click 'Exit' to leave the assessment platform.</p>"
        );
      } else {
        $("#assessContainer").html(response);
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
 *  EXIT ASSESSMENT PLATFORM
 *  Return to the dashboard
 */
function exitPlatform() {

  if (confirm("Are you sure you want to leave the platform?\n" +
    "If you have not submitted any answers for a test they will be lost.")) {
    window.location.replace(baseURL + "dashboard");
  }
}
