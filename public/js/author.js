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
      sa: $('#singleAnswer').val(),
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
