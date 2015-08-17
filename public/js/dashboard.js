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
      "<div id=\"leftmost-vis-control\" class=\"dash-control visualisation-control\" onclick=\"getQuestionList();\">" +
        "<p>QUESTIONS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"getTestList();\">" +
        "<p>TESTS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"alert('students');\">" +
        "<p>STUDENTS</p>" +
      "</div>" +
      "<div class=\"dash-control visualisation-control\" onclick=\"alert('classes');\">" +
        "<p>CLASSES</p>" +
      "</div>"
    );

    $('#visualisations-container').show();

  } else {

    // shrink header
    $('header').css({
      'height' : '60px'
    });

    // empty HTML from advanced options container
    $('#advancedOptions').html('');

    $('#visualisations-container').hide();
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
        "<select id=\"question-choice\" class=\"vis-data-choice\">" +
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
 *  GET LIST OF TESTS FOR VISUALISATIONS
 *  Show options for test-specific data visualisations
 */
function getTestList() {

  clearVisualisationContainer();

  // get list of tests via Ajax
  $.ajax({
    url: baseURL + "dashboard/getAssessorsTestList",
    type: "GET",
    dataType: "json",
    success: function(response) {

      // create a select element and seperate container for graphics/information
      $("#visualisations-container").html(
        "<select id=\"test-choice\" class=\"vis-data-choice\">" +
          "<option value=\"\">Please choose one of your tests from this list: </option>" +
        "</select>" +
        "<div id=\"test-data-container\"></div>"
      );

      // append each test
      for (var item in response) {
        $('#test-choice').append(
          "<option value=\"" + item + "\"> * " + response[item] + "</option>"
        );
      }

      // set a change listener to call the next function
      $('#test-choice').change(function() {
        loadTestVisualisations($(this).val());
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
        $("#question-data-container").html(
          "<div id=\"pie-container\">" +
            "<div id=\"pie-left\" class=\"three-pie\">" +
              "<div class=\"pie-desc-container\">" +
                "<div class=\"pie-desc\">" +
                "<p>Understanding of Questions</p>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-uq\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Question Understood</p>" +
                  "</div>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-dnuq\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Question Not Understood</p>" +
                  "</div>" +
                "</div>" +
              "</div>" +
            "</div>" +
            "<div id=\"pie-middle\" class=\"three-pie\">" +
              "<div class=\"pie-desc-container\">" +
                "<div class=\"pie-desc\">" +
                "<p>Correct Answers</p>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-ca\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Correctly Answered</p>" +
                  "</div>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-wa\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Incorrectly Answered</p>" +
                  "</div>" +
                "</div>" +
              "</div>" +
            "</div>" +
            "<div id=\"pie-right\" class=\"three-pie\">" +
              "<div class=\"pie-desc-container\">" +
                "<div class=\"pie-desc\">" +
                "<p>Understanding of Feedback</p>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-uf\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Feedback Understood</p>" +
                  "</div>" +
                "</div>" +
                "<div class=\"pie-legend\">" +
                  "<div id=\"plc-dnuf\" class=\"pie-legend-colour\"></div>" +
                  "<div class=\"pie-legend-text\">" +
                    "<p>Feedback Not Understood</p>" +
                  "</div>" +
                "</div>" +
              "</div>" +
            "</div>" +
          "</div>" +
          "<div id=\"student-question-table-container\"></div>"
        );

        // compute values and draw corresponding pie charts for uq, ca and uf
        drawPie("pie-left", computeTotalUQ(response, "question"));
        drawPie("pie-middle", computeTotalCA(response, "question"));
        drawPie("pie-right", computeTotalUF(response, "question"));

        // insert html table
        $('#student-question-table-container').html(
          "<table>" +
            "<thead>" +
              "<tr>" +
                "<th>Student</th>" +
                "<th>UQ</th>" +
                "<th>CA</th>" +
                "<th>UF</th>" +
              "</tr>" +
            "</thead>" +
            "<tbody id=\"question-table-body\">" +
            "</tbody>" +
          "</table>"
        );

        // loop through response; add table row per entry
        for (var student in response) {
          $('#question-table-body').append(
            "<tr>" +
              "<td>" + student + "</td>" +
              "<td>" + response[student]["uq"] + "</td>" +
              "<td>" + response[student]["ca"] + "</td>" +
              "<td>" + response[student]["uf"] + "</td>" +
            "</tr>"
          );
        }
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
 *  LOAD SINGLE TEST VISUALISATIONS
 *  Request data via Ajax, draw scatter plot and 2x pie charts
 */
var loadTestVisualisations = function(value) {

  if (value) {

    // fetch question data via Ajax
    $.ajax({
      url: baseURL + "dashboard/getTestData",
      data: {
        tId: value
      },
      type: "POST",
      dataType: "json",
      success: function(response) {

        // append new containers for visualisations
        $("#test-data-container").html(
          "<div id=\"test-vis-container\">" +
            "<div id=\"scatterplot\" class=\"large-scatterplot\"></div>" +
            "<div id=\"two-pie-container\">" +
              "<div id=\"pie-top-right\" class=\"two-pie\">" +
                "<div class=\"pie-desc-container\">" +
                  "<div class=\"pie-desc\">" +
                  "<p>Understanding of Questions</p>" +
                  "</div>" +
                  "<div class=\"pie-legend\">" +
                    "<div id=\"plc-uq\" class=\"pie-legend-colour\"></div>" +
                    "<div class=\"pie-legend-text\">" +
                      "<p>Questions Understood</p>" +
                    "</div>" +
                  "</div>" +
                  "<div class=\"pie-legend\">" +
                    "<div id=\"plc-dnuq\" class=\"pie-legend-colour\"></div>" +
                    "<div class=\"pie-legend-text\">" +
                      "<p>Questions Not Understood</p>" +
                    "</div>" +
                  "</div>" +
                "</div>" +
              "</div>" +
              "<div id=\"pie-bottom-right\" class=\"two-pie\">" +
                "<div class=\"pie-desc-container\">" +
                  "<div class=\"pie-desc\">" +
                  "<p>Understanding of Feedback</p>" +
                  "</div>" +
                  "<div class=\"pie-legend\">" +
                    "<div id=\"plc-uf\" class=\"pie-legend-colour\"></div>" +
                    "<div class=\"pie-legend-text\">" +
                      "<p>Feedback Understood</p>" +
                    "</div>" +
                  "</div>" +
                  "<div class=\"pie-legend\">" +
                    "<div id=\"plc-dnuf\" class=\"pie-legend-colour\"></div>" +
                    "<div class=\"pie-legend-text\">" +
                      "<p>Feedback Not Understood</p>" +
                    "</div>" +
                  "</div>" +
                "</div>" +
              "</div>" +
            "</div>" +
          "</div>"
        );

        // compute values and draw corresponding pie charts for uq and uf
        drawPie("pie-top-right", computeTotalUQ(response, "test"));
        drawPie("pie-bottom-right", computeTotalUF(response, "test"));

        drawScatterplot();
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
 *  Written for question/test data to be passed
 */
var computeTotalUQ = function(data, usage) {

  // prepare object of values to return
  totals = {};
  totals.uq = 0;
  totals.dnuq = 0;

  if (usage === "question") {

    for (var user in data) {
      if (data[user]["uq"] === 1) {
        totals.uq++;
      } else {
        totals.dnuq++;
      }
    }

  } else if (usage === "test") {

    for (var user in data.userData) {

      // add understanding of questions to overall total
      totals.uq += data.userData[user]["uq"];

      // calculate lack of understanding of questions by deducting from no. questions
      totals.dnuq += (data.testData["totalQuestions"] - data.userData[user]["uq"]);
    }

  } else {

    throw new Exception("Unrecognised usage.");
  }

  return totals;
}

/**
 *  COMPUTE TOTAL VALUES FOR 'CORRECT ANSWERS' (ca)
 *  Written for question/test data to be passed
 */
var computeTotalCA = function(data, usage) {

  // prepare object of values to return
  totals = {};
  totals.ca = 0;
  totals.wa = 0;

  if (usage === "question") {

    for (var user in data) {
      if (data[user]["ca"] === 1) {
        totals.ca++;
      } else {
        totals.wa++;
      }
    }

  } else {

    throw new Exception("Unrecognised usage.");
  }

  return totals;
}

/**
 *  COMPUTE TOTAL VALUES FOR 'UNDERSTANDING OF FEEDBACK' (uf)
 *  Written for question/test data to be passed
 */
var computeTotalUF = function(data, usage) {

  // prepare object of values to return
  totals = {};
  totals.uf = 0;
  totals.dnuf = 0;

  if (usage === "question") {

    // additionally check if understanding of feedback was provided (sometimes n/a)
    for (var user in data) {
      if (typeof data[user]["uf"] !== 'undefined') {
        if (data[user]["uf"] === 1) {
          totals.uf++;
        } else {
          totals.dnuf++;
        }
      }
    }

  } else if (usage === "test") {

    // TODO: consider getting this method to return NO DATA for drawPie to handle
    for (var user in data.userData) {
      if (typeof data.userData[user]["uf"] !== 'undefined') {

        // add understanding of feedback to overall total
        totals.uf += data.userData[user]["uf"];

        // calculate lack of understanding of feedback by deducting from total q's minus correct answers
        totals.dnuf += ((data.testData["totalQuestions"] - data.userData[user]["ca"]) - data.userData[user]["uf"]);
      }
    }

  } else {

    throw new Exception("Unrecognised usage.");
  }

  return totals;
}

/**
 *  DRAW PIE CHART
 *  Pass container id and data to draw a pie chart within that container
 */
var drawPie = function(divId, data) {

  // change array based on data received & color scheme
  var pieData = [];
  var color;
  if (data.uq) {
    pieData[0] = data.uq;
    pieData[1] = data.dnuq;
    color = d3.scale.ordinal()
      .range(["#FFFF00", "#990099"]);
  } else if (data.uf) {
    pieData[0] = data.uf;
    pieData[1] = data.dnuf;
    color = d3.scale.ordinal()
      .range(["#0033CC", "#FF9900"]);
  } else {
    pieData[0] = data.ca;
    pieData[1] = data.wa;
    color = d3.scale.ordinal()
      .range(["#00CC00", "#CC0000"]);
  }

  var width = 300,
    height = 300,
    radius = height / 2 - 10;

  var arc = d3.svg.arc()
    .innerRadius(0)
    .outerRadius(radius);

  var pie = d3.layout.pie();

  var svg = d3.select("#" + divId).append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
      .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

  var g = svg.selectAll(".arc")
    .data(pie(pieData))
    .enter().append("g")
      .attr("class", "arc");

  g.append("path")
    .style("fill", function(d, i) { return color(i); })
    .attr("d", arc);

  g.append("text")
    .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")" })
    .attr("dy", ".35em")
    .style("text-anchor", "middle")
    .text(function(d) { return Math.round(d.data / (pieData[0] + pieData[1]) * 100) + "%"; });
}

/**
 *  TEST: TODO, refactor me
 */
var drawScatterplot = function() {

  // http://bl.ocks.org/mbostock/3887118
  var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = 600 - margin.left - margin.right,
    height = 600 - margin.top - margin.bottom;

  var x = d3.scale.linear()
    .range([0, width]);

  var y = d3.scale.linear()
    .range([height, 0]);

  var color = d3.scale.category10();

  var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom")
    .tickFormat(d3.format("d"))
    .tickSubdivide(0);

  var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .tickFormat(d3.format("d"))
    .tickSubdivide(0);

  var svg = d3.select("#scatterplot").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  d3.json("public/js/scatterplot_ex1.json", function(error, data) {
    if (error) throw error;

    // ???
    data.forEach(function(d) {
      d.score = +d.score;
      d.user = +d.user;
    });

    // ???
    x.domain(d3.extent(data, function(d) { return d.user; })).nice();
    y.domain(d3.extent(data, function(d) { return d.score; })).nice();

    var xAxisSVG = svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis)
      .append("text")
        .attr("class", "label")
        .attr("x", width)
        .attr("y", -6)
        .style("text-anchor", "end")
        .text("User Id");

    console.log(xAxisSVG);

    svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
      .append("text")
        .attr("class", "label")
        .attr("transform", "rotate(-90)")
        .attr("y", 6)
        .attr("dy", ".71em")
        .style("text-anchor", "end")
        .text("Score");

    svg.selectAll(".dot")
      .data(data)
      .enter().append("circle")
        .attr("class", "dot")
        .attr("r", 3.5)
        .attr("cx", function(d) {
          return x(d.user);
        })
        .attr("cy", function(d) {
          return y(d.score);
        })
        .style("fill", function(d) {
          return color(d.test);
        });

    var legend = svg.selectAll(".legend")
      .data(color.domain())
      .enter().append("g")
        .attr("class", "legend")
        .attr("transform", function(d, i) {
          return "translate(0," + i * 20 + ")";
        });

    legend.append("rect")
      .attr("x", width - 18)
      .attr("width", 18)
      .attr("height", 18)
      .style("fill", color);

    legend.append("text")
      .attr("x", width - 24)
      .attr("y", 9)
      .attr("dy", ".35em")
      .style("text-anchor", "end")
      .text(function(d) { return d; });
  });
}
