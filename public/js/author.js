/**
 *  AUTHOR.JS
 *  @author Jonathan Lamb
 */

var multipleQuestionCount = 0;
var questionsJSON;
var testsJSON;
var groupsJSON;
var regex;
var patternTestString;

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
      if (questionType === "pattern") {
        testRegex();
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
 *  REGULAR EXPRESSION TEST FIELD
 *  Add key listeners to test Regular Expressions for Pattern Matching questions
 */
var testRegex = function() {

  $('#regex').keyup(function() {
    regex = new RegExp($(this).val());
  });
  $('#testPattern').keyup(function() {
    patternTestString = $(this).val();
    if (typeof patternTestString !== 'undefined') {
      if (patternTestString.match(regex) !== null) {
        $('#testPattern').css({
          'color' : '#009900',
          'font-weight' : 'bold'
        });
      } else if (patternTestString == '') {
        $('#testPattern').css({
          'color' : '#383838',
          'font-weight' : 'normal'
        });
      } else {
        $('#testPattern').css({
          'color' : '#CC0000',
          'font-weight' : 'normal'
        });
      }
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
  if (typeof feedback === 'undefined' || feedback == '') {
    $("#authorContainer").append(
      "<p>Feedback was not provided with this question.</p>"
    );
  } else {
    $("#authorContainer").append(
      "<p><span class=\"info-heading\">Feedback</span>: " + feedback + "</p>"
    );
  }

  // append exit button to return to manage questions table
  $("#authorContainer").append(
    "<button onclick=\"manageQuestions();\">RETURN</button>"
  );
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
 *  LOAD USERS FOR GROUP CREATION
 *  Load a list of students so assessors may create groups for mass test distribution
 */
function loadUsersForGroupCreation() {

  $.ajax({
    url: baseURL + "author/getStudents",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // create form with drag and drop lists
      $("#authorContainer").html(
        "<h2>Create Group</h2>" +
        "<form id=\"testForm\" onsubmit=\"createGroup(); return false;\">" +
          "<label for=\"testName\">Provide a name for your group (required):</label><br>" +
          "<input id=\"testName\" required type=\"text\" autocomplete=\"off\" " +
            "placeholder=\"e.g. Object-oriented Programming 1\" pattern=\"[\\w\\s,]+\" maxlength=\"35\"><br><br>" +
          "<p>Drag and drop students from the left-hand list to the right-hand list to include them in your group.</p>" +
          "<div id=\"selectionLists\">" +
            "<ul id=\"sortable1\" class=\"connectedSortable\"></ul>" +
            "<ul id=\"sortable2\" class=\"connectedSortable\"></ul>" +
          "</div>" +
        "</form>"
      );

      // connect drag and drop lists using jQuery
      $("#sortable1, #sortable2").sortable({
    		connectWith: ".connectedSortable"
      }).disableSelection();

      // add users to the left hand list
      for (var user in response) {
        $('#sortable1').append(
          "<li id=\"" + user + "\">" + response[user]["user_name"] +
            ": " + response[user]["full_name"] + "</li>"
        );
      }

      // append form submission button
      $("#testForm").append(
        "<br><input type=\"submit\" value=\"Create Group\">"
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
 *  CREATE STUDENT GROUP
 *  Create a user distribution group
 */
function createGroup() {

  // prepare an array of selected user id's and convert to JSON
  var users = [];
  $('#sortable2 li').each(function() {
    users.push($(this).attr('id'));
  });
  users = JSON.stringify(users);

  // create a test if at least one user was selected
  if (users !== '[]') {

    $.ajax({
      url: baseURL + "author/createGroup",
      data: {
        gn: $('#testName').val(),
        us: users
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
 *  MANAGE GROUPS
 *  Get further details for or delete distribution groups
 */
function manageGroups() {

  $.ajax({
    url: baseURL + "author/getGroups",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set JSON of groups as response for re-use
      groupsJSON = response;

      // set container header
      $("#authorContainer").html("<h2>Manage Groups</h2>");

      // append a table to the container
      $('#authorContainer').append(
        "<table>" +
          "<thead>" +
            "<tr>" +
              "<th>Unique ID</th>" +
              "<th>Name</th>" +
              "<th>No. Users</th>" +
              "<th></th>" +   // deliberately left blank
              "<th></th>" +
            "</tr>" +
          "</thead>" +
          "<tbody id=\"manage-groups-table-body\">" +
          "</tbody>" +
        "</table>"
      );

      // create and append a representation of each group to the container
      for (var group in response) {
        $("#manage-groups-table-body").append(
          "<tr>" +
            "<td class=\"table-mongo-id\">" + group + "</td>" +
            "<td>" + response[group]["name"] + "</td>" +
            "<td>" + response[group]["members"].length + "</td>" +
            "<td class=\"table-button-container\"><button onclick=\"getGroupInfo('" + group + "');\">INFO</button></td>" +
            "<td class=\"table-button-container\"><button onclick=\"deleteGroup('" + group + "');\">DELETE</button></td>" +
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
 *  GET GROUP INFORMATION
 *  Provide further information about a group
 */
function getGroupInfo(groupId) {

  // request group details via Ajax request
  $.ajax({
    url: baseURL + "author/getGroupMemberDetails",
    data: {
      gId: groupId
    },
    type: "POST",
    dataType: "json",
    success: function (response) {

      // update container with question information
      $("#authorContainer").html(
        "<h2>Group Information</h2>" +
        "<p><span class=\"info-heading\">ID</span>: " + groupId + "</p>" +
        "<p><span class=\"info-heading\">Name</span>: " + groupsJSON[groupId]["name"] + "</p>"
      );

      // add member details
      $("#authorContainer").append(
        "<h3>Members</h3>"
      );
      for (var user in response) {
        $("#authorContainer").append(
          "<p><span class=\"info-heading\">Name: </span>" +
            response[user] + "<br>" +
            "<span class=\"info-heading\">ID: </span>" +
            user + "</p>"
        );
      }

      // append exit button to return to manage questions table
      $("#authorContainer").append(
        "<br><button onclick=\"manageGroups();\">RETURN</button>"
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
 *  DELETE GROUP
 *  Request to delete a group based on MongoId
 */
function deleteGroup(groupId) {

  var deleteGroup = prompt("Are you sure you want to delete group Id: " + groupId + "?\n" +
    "Enter the word 'DELETE' in upper case to delete this data.");
  if (deleteGroup === "DELETE") {

    $.ajax({
      url: baseURL + "author/deleteGroup",
      data: {
        gId: groupId
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

      // create form with drag and drop lists
      $("#authorContainer").html(
        "<h2>Create Test</h2>" +
        "<form id=\"testForm\" onsubmit=\"createTest(); return false;\">" +
          "<label for=\"testName\">Provide a name for your test (required):</label><br>" +
          "<input id=\"testName\" required type=\"text\" autocomplete=\"off\" " +
            "placeholder=\"e.g. Object-oriented Programming 1\" pattern=\"[\\w\\s,]+\" maxlength=\"35\"><br><br>" +
          "<p>Drag and drop questions from the left-hand list to the right-hand list to include them in your test.<br>" +
            "Re-arrange your questions in the right-hand list to change the order in which they appear in the test.</p>" +
          "<div id=\"selectionLists\">" +
            "<ul id=\"sortable1\" class=\"connectedSortable\"></ul>" +
            "<ul id=\"sortable2\" class=\"connectedSortable\"></ul>" +
          "</div>" +
        "</form>"
      );

      // connect drag and drop lists using jQuery
      $("#sortable1, #sortable2").sortable({
    		connectWith: ".connectedSortable"
      }).disableSelection();

      // add questions to the left hand list
      for (var question in response) {
        $('#sortable1').append(
          "<li id=\"" + question + "\">" + response[question]["name"] +
            " (" + response[question]["schema"].toUpperCase() + ")</li>"
        );
      }

      // append form submission button
      $("#testForm").append(
        "<br><input type=\"submit\" value=\"Create Test\">"
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
  $('#sortable2 li').each(function() {
    questions.push($(this).attr('id'));
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

  // inner function to replace cell value with image
  var replaceTakenData = function(item) {

    if (typeof item === 'undefined') {
      return "<img src=\"" + baseURL + "public/img/cross.png\"></img>";
    } else {
      return "<img src=\"" + baseURL + "public/img/tick.png\"></img>";
    }
  }

  $.ajax({
    url: baseURL + "author/getTests",
    type: "GET",
    dataType: "json",
    success: function (response) {

      // set JSON of questions as response for re-use
      testsJSON = response;

      // set container header
      $("#authorContainer").html("<h2>Manage Tests</h2>");

      // append a table to the container
      $('#authorContainer').append(
        "<table>" +
          "<thead>" +
            "<tr>" +
              "<th>Unique ID</th>" +
              "<th>Name</th>" +
              "<th>No. Q's</th>" +
              "<th>Taken</th>" +
              "<th></th>" +   // deliberately left blank
              "<th></th>" +
              "<th></th>" +
            "</tr>" +
          "</thead>" +
          "<tbody id=\"manage-tests-table-body\">" +
          "</tbody>" +
        "</table>"
      );

      // create and append a representation of each question to the container
      for (var test in response) {
        $("#manage-tests-table-body").append(
          "<tr>" +
            "<td class=\"table-mongo-id\">" + test + "</td>" +
            "<td>" + response[test]["name"] + "</td>" +
            "<td>" + response[test]["questions"].length + "</td>" +
            "<td>" + replaceTakenData(response[test]["taken"]) + "</td>" +
            "<td class=\"table-button-container\"><button onclick=\"getTestInfo('" + test + "');\">INFO</button></td>" +
            "<td class=\"table-button-container\"><button onclick=\"loadUsersForTest('" + test + "');\">ISSUE</button></td>" +
            "<td class=\"table-button-container\"><button onclick=\"deleteTest('" + test + "');\">DELETE</button></td>" +
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
 *  GET TEST INFORMATION
 *  Provide further information about a test
 */
function getTestInfo(testId) {

  // request test details via Ajax request
  $.ajax({
    url: baseURL + "author/getTestDetails",
    data: {
      tId: testId
    },
    type: "POST",
    dataType: "json",
    success: function (response) {

      // update container with question information
      $("#authorContainer").html(
        "<h2>Test Information</h2>" +
        "<p><span class=\"info-heading\">ID</span>: " + testId + "</p>" +
        "<p><span class=\"info-heading\">Name</span>: " + testsJSON[testId]["name"] + "</p>"
      );

      // add questions
      $("#authorContainer").append(
        "<h3 id=\"test-question-header\">Questions</h3>" +
        "<ol id=\"test-question-list\" class=\"question-info-list\"></ol>"
      );
      for (var question in response["questions"]) {
        $("#test-question-list").append(
          "<li><span class=\"info-heading\">Name: </span>'" +
            response["questions"][question]["name"] + "'<br>" +
            "<span class=\"info-heading\">Type: </span>" +
            response["questions"][question]["type"] + "<br>" +
            "<span class=\"info-heading\">Question: </span>\"" +
            response["questions"][question]["question"] + "\"</li>"
        );
      }

      // add users the test has been issued to
      if (typeof response["issued"] !== 'undefined') {
        if (response["issued"] != '') {
          $("#authorContainer").append(
            "<h3 id=\"test-question-header\">Issued to:</h3>" +
            "<ul id=\"test-issued-list\" class=\"question-info-list\"></ul>"
          );
          for (var user in response["issued"]) {
            $("#test-issued-list").append(
              "<li><span class=\"info-heading\">Name: </span>" +
                response["issued"][user] + "<br>" +
                "<span class=\"info-heading\">Unique ID: </span>" +
                user + "</li>"
            );
          }
        }
      }

      // add users the test has been taken by
      if (typeof response["taken"] !== 'undefined') {
        if (response["taken"] != '') {
          $("#authorContainer").append(
            "<h3 id=\"test-question-header\">Taken by:</h3>" +
            "<ul id=\"test-taken-list\" class=\"question-info-list\"></ul>"
          );
          for (var user in response["taken"]) {
            $("#test-taken-list").append(
              "<li><span class=\"info-heading\">Name: </span>" +
                response["taken"][user] + "<br>" +
                "<span class=\"info-heading\">Unique ID: </span>" +
                user + "</li>"
            );
          }
        }
      }

      // append exit button to return to manage questions table
      $("#authorContainer").append(
        "<br><button onclick=\"manageTests();\">RETURN</button>"
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

/*
 *  LOAD TESTS TO ALLOW TEST ISSUING (obsolete with updated 'manage tests')
 *  Load the users tests - allows an ID to be specified, returned to the server and availability to be returned

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
 */

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

      // header
      $("#authorContainer").html(
        "<h2>Issue Test</h2>"
      );

      // groups
      if ($.isEmptyObject(response["groups"])) {

        $("#authorContainer").append(
          "<h3>Groups</h3>" +
          "<p>There are no groups you can issue this test to.<br>" +
          "This may be because no groups have been created, the test has already been issued/taken by one or more group " +
          "members, or it has been completely issued to every available student.</p>"
        );

      } else {

        $("#authorContainer").append(
          "<h3>Groups</h3>" +
          "<p>The test may be issued to the following groups:</p>"
        );
        for (var group in response["groups"]) {
          $("#authorContainer").append(
            "<p><span class=\"table-mongo-id\">" + group + ":</span> " + response["groups"][group]
            + " &nbsp;<button onclick=\"issueTest('" +
            testId + "', '" + group + "','group')\">ISSUE</button></p>"
          );
        }
      }

      // individual students
      if ($.isEmptyObject(response["students"])) {

        // advise the test cannot be issued
        $("#authorContainer").append(
          "<h3>Individual Students</h3>" +
          "<p>There are no more available students to issue this test to.</p>"
        );

      } else {

        // set container header and prompt
        $("#authorContainer").append(
          "<h3>Individual Students</h3>" +
          "<p>The test may be issued to the following students:</p>"
        );

        // create an entry for each available user
        for (var user in response["students"]) {
          $("#authorContainer").append(
            "<p><span class=\"table-mongo-id\">" + user + ":</span> " + response["students"][user]
            + " &nbsp;<button onclick=\"issueTest('" +
            testId + "', '" + user + "','student')\">ISSUE</button></p>"
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
function issueTest(testId, userOrGroupId, usage) {

  $.ajax({
    url: baseURL + "author/issueTest",
    data: {
      tId: testId,
      ugId: userOrGroupId,
      u: usage
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
