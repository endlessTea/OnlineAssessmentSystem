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
  $('#visualisations-container').html('');
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

      // create a select element and seperate container for graphics/information
      $("#visualisations-container").html(
        "<select id=\"question-choice\">" +
          "<option value=\"\">Please choose one of your questions from this list: </option>" +
        "</select>" +
        "<div id=\"question-data-container\"></div>"
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
 *  Request data via Ajax, draw 3 pie charts and a summary table of information
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

        // append new containers for visualisations
        // "<p>" + JSON.stringify(response) + "</p>"+
        $("#question-data-container").html(
          "<div id=\"pie-container\">" +
            "<div id=\"pie-left\" class=\"three-pie\"></div>" +
            "<div id=\"pie-middle\" class=\"three-pie\"></div>" +
            "<div id=\"pie-right\" class=\"three-pie\"></div>" +
          "</div>" +
          "<div id=\"student-question-table\"></div>"
        );

        // compute values and draw corresponding pie charts for uq, ca and uf
        drawPie("pie-left", computeTotalUQ(response));
        drawPie("pie-middle", computeTotalCA(response));
        drawPie("pie-right", computeTotalUF(response));

        // draw table of information (pass div id)

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

/**
 *  COMPUTE TOTAL VALUES FOR 'UNDERSTANDING OF A QUESTION' (uq)
 *  Written for question data to be passed (TODO: refactor for later methods)
 */
var computeTotalUQ = function(data) {

  // prepare object of values to return
  totals = [];
  totals[0] = 0;
  totals[1] = 0;

  for (var user in data) {
    if (data[user]["uq"] === "1") {
      totals[0]++;
    } else {
      totals[1]++;
    }
  }

  return totals;
}

/**
 *  COMPUTE TOTAL VALUES FOR 'CORRECT ANSWERS' (ca)
 *  Written for question data to be passed (TODO: refactor for later methods)
 */
var computeTotalCA = function(data) {

  // prepare object of values to return
  totals = [];
  totals[0] = 0;
  totals[1] = 0;

  for (var user in data) {
    if (data[user]["ca"] === 1) {
      totals[0]++;
    } else {
      totals[1]++;
    }
  }

  return totals;
}

/**
 *  COMPUTE TOTAL VALUES FOR 'UNDERSTANDING OF FEEDBACK' (uf)
 *  Written for question data to be passed (TODO: refactor for later methods)
 */
var computeTotalUF = function(data) {

  // prepare object of values to return
  totals = [];
  totals[0] = 0;
  totals[1] = 0;

  // additionally check if understanding of feedback was provided (sometimes n/a)
  for (var user in data) {
    if (data[user]["uf"]) {
      if (data[user]["uf"] === "1") {
        totals[0]++;
      } else {
        totals[1]++;
      }
    }
  }

  return totals;
}

/**
 *  DRAW PIE CHART (ARC CORNERS) TODO: refactor to allow 100%/0% values to be drawn (use pie?)
 *  Pass container id and data to draw a pie chart within that container
 */
var drawPie = function(divId, data) {

  var width = 300,
    height = 300,
    radius = height / 2 - 10;

  var arc = d3.svg.arc()
    .innerRadius(radius - 80)
    .outerRadius(radius)
    .cornerRadius(20);

  var pie = d3.layout.pie()
    .padAngle(.03);

  // TODO (change color based on uq/ca/uf)
  var color = d3.scale.category10();

  var svg = d3.select("#" + divId).append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
      .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

  // http://bl.ocks.org/mbostock/3887193
  var g = svg.selectAll(".arc")
    .data(pie(data))
    .enter().append("g")
      .attr("class", "arc");

  g.append("path")
    .style("fill", function(d, i) { return color(i); })
    .attr("d", arc);

  g.append("text")
    .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")" })
    .attr("dy", ".35em")
    .style("text-anchor", "middle")
    .text(function(d) { return Math.round(d.data / (data[0] + data[1]) * 100) + "%"; });
}
