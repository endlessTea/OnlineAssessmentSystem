/**
 *  AUTHOR.JS
 *  @author Jonathan Lamb
 */

var multipleQuestionCount = 0;
var questionsJSON;

/**
 *  GET QUESTION TEMPLATE
 *  Request HTML question template via Ajax
 */
function getQuestionTemplate(questionType) {

  // reset multiple var count
  multipleQuestionCount = 0;

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

  // send different ajax requests depending on the question type
  switch (questionType) {

    case "boolean":

      $.ajax({
        url: baseURL + "author/createQuestion",
        data: {
          qt: questionType,
          qn: $('#questionName').val(),
          qu: $('#question').val(),
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

      break;

    case "multiple":

      if (multipleQuestionCount < 2) {

        alert("You must provide at least 2 options for this question.");

      } else {

        // prepare choices and answers
        var options = [];
        var correctAnswers = [];
        $('#multiple-answers-container input[type="text"]').each(function() {
          options.push($(this).val());
        });
        $('#multiple-answers-container input[type="checkbox"]:checked').each(function() {
          correctAnswers.push($(this).attr('name'));
        });
        options = JSON.stringify(options);
        correctAnswers = JSON.stringify(correctAnswers);

        $.ajax({
          url: baseURL + "author/createQuestion",
          data: {
            qt: questionType,
            qn: $('#questionName').val(),
            qu: $('#question').val(),
            op: options,
            ca: correctAnswers,
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

      break;

    case "pattern":

      $.ajax({
        url: baseURL + "author/createQuestion",
        data: {
          qt: questionType,
          qn: $('#questionName').val(),
          qu: $('#question').val(),
          rx: "/" + $('#regex').val() + "/",
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

      break;

      case "short":

        $.ajax({
          url: baseURL + "author/createQuestion",
          data: {
            qt: questionType,
            qn: $('#questionName').val(),
            qu: $('#question').val(),
            ans: $('#answer').val(),
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

        break;

    default:
      alert("Question type unrecognised, please contact the system administrator.");
  }
}

/**
 *  ADD MULTIPLE QUESTION
 *  If creating multiple choice question, append input to multiple choice container
 */
function addMultipleOption() {

  // append input
  $('#multiple-answers-container').append(
    "<div id=\"ans-" + multipleQuestionCount + "\">" +
      "<input id=\"text-" + multipleQuestionCount + "\" required " +
        "type=\"text\" autocomplete=\"off\" placeholder=\"Type an option here and check the box if it is a correct answer\">" +
      "<input type=\"checkbox\" name=\"" + multipleQuestionCount + "\">" +
    "</div>"
  );

  // increment multiple question count
  multipleQuestionCount++;
}

/**
 *  REMOVE MULTIPLE QUESTION
 *  Decrement question counter and remove corresponding container
 */
function removeMultipleOption() {

  if (multipleQuestionCount >= 0) {

    multipleQuestionCount--;
    $('#ans-' + multipleQuestionCount).remove();
  }

  // reset to 0 if all multiple questions were removed
  if (multipleQuestionCount < 0) multipleQuestionCount = 0;
}

/**
 *  MANAGE QUESTIONS
 *  Allow questions to be deleted - TODO: refactor to allow questions to be updated
 */
function manageQuestions() {

  // inner function to replace cell value with image
  var replaceTakenData = function(item) {

    if (typeof item === 'undefined') {
      return "<img src=\"" + baseURL + "public/img/cross.png\"></img>";
    } else {
      return "<img src=\"" + baseURL + "public/img/tick.png\"></img>";
    }
  }

  $.ajax({
    url: baseURL + "author/getQuestions",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set JSON of questions as response for re-use
      questionsJSON = response;

      // set container header
      $("#authorContainer").html("<h2>Manage Questions</h2>");

      // append a table to the container
      $('#authorContainer').append(
        "<table>" +
          "<thead>" +
            "<tr>" +
              "<th>Unique ID</th>" +
              "<th>Name</th>" +
              "<th>Type</th>" +
              "<th>Taken</th>" +
              "<th></th>" +   // deliberately left blank
              "<th></th>" +
            "</tr>" +
          "</thead>" +
          "<tbody id=\"manage-question-table-body\">" +
          "</tbody>" +
        "</table>"
      );

      // create and append a representation of each question to the container
      for (var question in response) {
        $("#manage-question-table-body").append(
          "<tr>" +
            "<td class=\"table-mongo-id\">" + question + "</td>" +
            "<td>" + response[question]["name"] + "</td>" +
            "<td>" + response[question]["schema"].toUpperCase() + "</td>" +
            "<td>" + replaceTakenData(response[question]["taken"]) + "</td>" +
            "<td class=\"table-button-container\"><button onclick=\"getQuestionInfo('" + question + "')\">INFO</button></td>" +
            "<td class=\"table-button-container\"><button onclick=\"deleteQuestion('" + question + "')\">DELETE</button></td>" +
          "</tr>"
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
 *  GET QUESTION INFORMATION
 *  Provide further information about a question
 */
function getQuestionInfo(questionId) {

  // identify and store schema type
  var schema = questionsJSON[questionId]["schema"];

  // update container with question information
  $("#authorContainer").html(
    "<h2>Question Information</h2>" +
    "<p><span class=\"info-heading\">ID</span>: " + questionId + "</p>" +
    "<p><span class=\"info-heading\">Name</span>: " + questionsJSON[questionId]["name"] + "</p>" +
    "<p><span class=\"info-heading\">Type</span>: " + schema.toUpperCase() + "</p>" +
    "<p><span class=\"info-heading\">Question</span>: " + questionsJSON[questionId]["question"] + "</p>"
  );

  // add answers as per schema
  switch (schema) {

    case "boolean":
      $("#authorContainer").append(
        "<p><span class=\"info-heading\">Answer</span>: " + questionsJSON[questionId]["singleAnswer"] + "</p>"
      );
      break;

    case "multiple":
      $("#authorContainer").append(
        "<h3>Options / Answers</h3>" +
        "<p class=\"info-options-desc\">Correct options below are highlighted in green:</p>"
      );
      for (var option in questionsJSON[questionId]["options"]) {
        $("#authorContainer").append(
          "<div id=\"option-" + option + "\" class=\"option-container\">" +
            "<p>" + questionsJSON[questionId]["options"][option] + "</p>" +
          "</div>"
        );
        if (questionsJSON[questionId]["correctAnswers"].indexOf(option) > -1) {
          $('#option-' + option).css({
            'background-color' : '#B2E0B2'
          });
        }
      }
      break;

    case "pattern":
      $("#authorContainer").append(
        "<p><span class=\"info-heading\">Pattern</span>: " + questionsJSON[questionId]["pattern"] + "</p>"
      );
      break;

    case "short":
      $("#authorContainer").append(
        "<p><span class=\"info-heading\">Answer</span>: " + questionsJSON[questionId]["answer"] + "</p>"
      );
      break;

    default:
      alert("Question type unrecognised, please contact the system administrator.");
  }

  // add feedback if it has been provided
  var feedback = questionsJSON[questionId]["feedback"];
  if (typeof feedback === 'undefined') {
    $("#authorContainer").append(
      "<p>Feedback was not provided with this question.</p>"
    );
  } else {
    $("#authorContainer").append(
      "<p><span class=\"info-heading\">Feedback</span>: " + feedback + "</p>"
    );
  }
}

/**
 *  DELETE QUESTION
 *  Request to delete a question based on MongoId
 */
function deleteQuestion(questionId) {

  var deleteQuestion = prompt("Are you sure you want to delete '" + questionsJSON[questionId]["name"] + "'?\n\n" +
    "\n\n" +
    "If you're SURE you want to delete it, type 'DELETE' in upper case below and confirm.");
  if (deleteQuestion === "DELETE") {

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
        "<form id=\"testForm\" onsubmit=\"createTest(); return false;\">" +
          "<label for=\"testName\">Provide a name for your test (required):</label><br>" +
          "<input id=\"testName\" required type=\"text\" autocomplete=\"off\" " +
            "placeholder=\"e.g. Object-oriented Programming 1\" pattern=\"[\\w\\s,]+\">" +
          "<p>Check the boxes for the questions you want to include in your test:</p>" +
        "</form>"
      );

      // append each question to the form with checkbox input
      for (var question in response) {
        $("#testForm").append(
          "<div class=\"qField\">" +
            "<p>" + question + ": " + response[question]["question"] +
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
  questions = JSON.stringify(questions);

  // create a test if at least one question was selected
  if (questions !== '[]') {

    $.ajax({
      url: baseURL + "author/createTest",
      data: {
        tn: $('#testName').val(),
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
          "<p>" + test + ": " + response[test]["name"] + ", " + response[test]["questions"].length +
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

  var deleteTest = prompt("Are you sure you want to delete test Id: " + testId + "?\n" +
    "Enter the word 'DELETE' in upper case to delete this data.");
  if (deleteTest === "DELETE") {

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
          "<p>" + test + ": " + response[test]["name"] + ", " + response[test]["questions"].length +
          " questions &nbsp;<button onclick=\"loadUsersForTest('" +
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

      if ($.isEmptyObject(response)) {

        // advise the test cannot be issued
        $("#authorContainer").html(
          "<h2>Test Fully Issued</h2>" +
          "<p>Test '" + testId + "' has been issued to every available user.</p>"
        );

      } else {

        // set container header and prompt
        $("#authorContainer").html(
          "<h2>Issue Test</h2>" +
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

  if (confirm("Are you sure you want to leave the platform?\n" +
    "Make sure you have saved your questions or tests before leaving.")) {
    window.location.replace(baseURL + "dashboard");
  }
}
