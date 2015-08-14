/**
 *  DASHBOARD.JS
 *  @author Jonathan Lamb
 */

// toggle visualisations on and off
var visualisationsOn = false;

/**
 *  TOGGLE VISUALISATIONS
 *  Turn visualisation options on and off
 */
function toggleVisualisations() {

  visualisationsOn = !visualisationsOn;

  if (visualisationsOn) {

    // update page prompt if visualisations toggled
    $('#page-prompt').html('');

    // grow header
    $('header').css({
      'height' : '120px'
    });

    // create html
    $('#advancedOptions').html(
      "<div class=\"dash-control visualisation-control\" onclick=\"getQuestionList();\">" +
        "<p>QUESTIONS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"alert('tests');\">" +
        "<p>TESTS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"alert('students');\">" +
        "<p>STUDENTS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"alert('classes');\">" +
        "<p>CLASSES</p>" +
      "</div>"
    );

  } else {

    // shrink header
    $('header').css({
      'height' : '60px'
    });

    // empty HTML from advanced options container
    $('#advancedOptions').html('');
  }
}

/**
 *  CLEAR VISUALISATION CONTAINER
 *  Clear container for each time a new option is selected (i.e. questions to classes to students...)
 */
var clearVisualisationContainer = function() {
  $('#visualisations').html('');
}

/**
 *  GET LIST OF QUESTIONS FOR VISUALISATIONS
 *  Show options for question-specific data visualisations
 */
function getQuestionList() {

  clearVisualisationContainer();

  // get list of questions via Ajax
  $.ajax({
    url: baseURL + "dashboard/getAssessorsQuestionList",
    type: "GET",
    dataType: "json",
    success: function(response) {

      // create a select element
      $("#visualisations").html(
        "<select id=\"question-choice\">" +
          "<option value=\"\">Please choose one of your questions from this list: </option>" +
        "</select>"
      );

      // append each question
      for (var item in response) {
        $('#question-choice').append(
          "<option value=\"" + item + "\"> * " + response[item] + "</option>"
        );
      }

      // set a change listener to call the next function
      $('#question-choice').change(function() {
        loadQuestionVisualisations($(this).val());
      });

    },
    error: function (request, status, error) {
      $('#page-prompt').html('');
      $("#visualisations").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  LOAD SINGLE QUESTION VISUALISATIONS
 *
 */
var loadQuestionVisualisations = function(value) {

  if (value) {

    // fetch question data via Ajax
    $.ajax({
      url: baseURL + "dashboard/getQuestionData",
      data: {
        qId: value
      },
      type: "POST",
      dataType: "json",
      success: function(response) {

        // append new containers for visualisations TODO seperate containers for question select and vis
        $("#visualisations").append(
          "success"
        );

        // draw pie charts (pass containers)


        // draw table of information (pass container)

      },
      error: function (request, status, error) {
        $("#visualisations").html(
          "<p>There was a problem with the request, please contact the system administrator: <br>" +
          request.responseText + "</p>"
        );
      }
    });
  }
}
