/**
 *  AUTHOR.JS
 *  @author Jonathan Lamb
 */

/**
 *  GET QUESTION TEMPLATE
 *  Request HTML question template via Ajax
 */
function getQuestionTemplate(questionType) {

  $.ajax({
    url: baseURL + "author/getQuestionTemplate",
    data: {
      qt: questionType
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#authorContainer").html(response);
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  CREATE QUESTION
 *  Create boolean question - TODO: refactor this to allow for different question types
 */
function createQuestion(questionType) {

  $.ajax({
    url: baseURL + "author/createQuestion",
    data: {
      qt: questionType,
      st: $('#statement').val(),
      sa: $('input[type="radio"][name="singleAnswer"]:checked').val(),
      fb: $('#feedback').val()
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#authorContainer").html(response);
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  MANAGE QUESTIONS
 *  Allow questions to be deleted - TODO: refactor to allow questions to be updated
 */
function manageQuestions() {

  $.ajax({
    url: baseURL + "author/getQuestions",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set container header
      $("#authorContainer").html("<h2>Manage Questions</h2>");

      // create and append a representation of each question to the container
      for (var question in response) {
        $("#authorContainer").append(
          "<p>" + question + ": " + response[question]["statement"] +
          "&nbsp;<button onclick=\"deleteQuestion('" +
          question + "')\">DELETE</button></p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  DELETE QUESTION
 *  Request to delete a question based on MongoId
 */
function deleteQuestion(questionId) {

  $.ajax({
    url: baseURL + "author/deleteQuestion",
    data: {
      qId: questionId
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#authorContainer").html(response);
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  LOAD QUESTIONS FOR NEW TEST
 *  Get the user's questions ready to create a new test
 */
function loadQuestionsForTestCreation() {

  $.ajax({
    url: baseURL + "author/getQuestions",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set container header
      $("#authorContainer").html("<h2>Create Test</h2>");

      // create a form
      $("#authorContainer").append(
        "<form id=\"testForm\" onsubmit=\"createTest(); return false;\"></form>"
      );

      // append each question to the form with checkbox input
      for (var question in response) {
        $("#testForm").append(
          "<div class=\"qField\">" +
            "<p>" + question + ": " + response[question]["statement"] +
            "&nbsp;<input type=\"checkbox\" name=\"" + question + "\">" +
          "</div>"
        );
      }

      // append form submission button
      $("#testForm").append(
        "<input type=\"submit\" value=\"Create Test\">"
      );
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  CREATE TEST
 *  Create test using existing questions
 */
function createTest() {

  // prepare an array of selected question id's and convert to JSON
  var questions = [];
  $('#testForm input:checked').each(function() {
    questions.push($(this).attr('name'));
  });
  questions = JSON.stringify(questions);   //.replace("[", "{").replace("]", "}")

  // create a test if at least one question was selected
  if (questions !== '[]') {

    $.ajax({
      url: baseURL + "author/createTest",
      data: {
        qs: questions
      },
      type: "POST",
      dataType: "html",
      success: function (response) {
        $("#authorContainer").html(response);
      },
      error: function (request, status, error) {
        $("#authorContainer").html(
          "<p>There was a problem with the request, please contact the system administrator: <br>" +
          request.responseText + "</p>"
        );
      }
    });
  }
}

/**
 *  MANAGE TESTS
 *  Allow tests to be deleted - TODO: refactor to allow tests to be updated
 */
function manageTests() {

  $.ajax({
    url: baseURL + "author/getTests",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set container header
      $("#authorContainer").html("<h2>Manage Tests</h2>");

      // create and append a representation of each question to the container
      for (var test in response) {
        $("#authorContainer").append(
          "<p>" + test + ": " + response[test]["questions"].length +
          " questions &nbsp;<button onclick=\"deleteTest('" +
          test + "')\">DELETE</button></p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  DELETE TEST
 *  Request to delete a question based on MongoId
 */
function deleteTest(testId) {

  $.ajax({
    url: baseURL + "author/deleteTest",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#authorContainer").html(response);
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  LOAD TESTS TO ALLOW TEST ISSUING
 *  Load the users tests - allows an ID to be specified, returned to the server and availability to be returned
 */
function loadTests() {

  $.ajax({
    url: baseURL + "author/getTests",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set container header
      $("#authorContainer").html("<h2>Select Test</h2>");

      // create and append a representation of each question to the container
      for (var test in response) {
        $("#authorContainer").append(
          "<p>" + test + " &nbsp;<button onclick=\"loadUsersForTest('" +
          test + "')\">SELECT</button></p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  LOAD USERS FOR TEST ISSUING
 *  Get limited subset of user details to allow tests to be issued
 */
function loadUsersForTest(testId) {

  $.ajax({
    url: baseURL + "author/getStudentsForTest",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "json",
    success: function (response) {

      // set container header
      $("#authorContainer").html("<h2>Issue Test</h2>");

      // create a form
      $("#authorContainer").append(
        "<p>Test '" + testId + "' is available to issue to:</p>"
      );

      // create an entry for each available user
      for (var user in response) {
        $("#authorContainer").append(
          "<p>" + user + ": " + response[user]
          + " &nbsp;<button onclick=\"issueTest('" +
          testId + "', '" + user + "')\">ISSUE</button></p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  ISSUE TEST
 *  Issue a tests to another user
 */
function issueTest(testId, userId) {

  $.ajax({
    url: baseURL + "author/issueTest",
    data: {
      tId: testId,
      sId: userId
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#authorContainer").html(response);
    },
    error: function (request, status, error) {
      $("#authorContainer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  EXIT AUTHORING PLATFORM
 *  Return to the dashboard
 */
function exitPlatform() {

  window.location.replace(baseURL + "dashboard");
}
