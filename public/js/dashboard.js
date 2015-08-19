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
      "<div id=\"rightmost-vis-control\" class=\"dash-control visualisation-control\" onclick=\"getTestList();\">" +
        "<p>TESTS</p>" +
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
        "<select id=\"question-choice\" class=\"vis-data-choice\">" +
          "<option value=\"\">Please choose one of your tests from this list: </option>" +
        "</select>" +
        "<div id=\"question-data-container\"></div>"
      );

      // append each test
      for (var item in response) {
        $('#question-choice').append(
          "<option value=\"" + item + "\"> * " + response[item] + "</option>"
        );
      }

      // set a change listener to call the next function
      $('#question-choice').change(function() {
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

        // draw table of information
        drawTable(response, "question");
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
        drawPie("pie-left", computeTotalUQ(response, "test"));
        drawPie("pie-middle", computeTotalCA(response, "test"));
        drawPie("pie-right", computeTotalUF(response, "test"));

        // draw table of information
        drawTable(response, "test");
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

  } else if (usage === "test") {

    for (var user in data.userData) {

      totals.ca += data.userData[user]["ca"];
      totals.wa += (data.testData["totalQuestions"] - data.userData[user]["ca"]);
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
    .attr("d", arc)
    .transition()
      .ease("elastic")
      .duration(500)
      .attrTween("d", tweenPie);

  g.append("text")
    .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")" })
    .attr("dy", ".35em")
    .style("text-anchor", "middle")
    .style("font-weight", "bold")
    .style("font-size", "1.1em")
    .text(function(d) {
      var value = Math.round(d.data / (pieData[0] + pieData[1]) * 100);
      if (value > 0) return value + "%";
      else return "";
    });

  // http://bl.ocks.org/mbostock/4341574
  function tweenPie(b) {
    b.innerRadius = 0;
    var i = d3.interpolate({startAngle: 0, endAngle: 0}, b);
    return function(t) { return arc(i(t)); };
  }
}

/**
 *  DRAW TABLE OF INFORMATION ABOUT STUDENTS
 */
var drawTable = function(data, usage) {

  // use function to replace 1, 0 and 'undefined' with image or text
  var replaceQuestionData = function(value) {

    switch (value) {

      case 0:
        return "<img src=\"" + baseURL + "public/img/cross.png\"></img>";
        break;

      case 1:
        return "<img src=\"" + baseURL + "public/img/tick.png\"></img>";
        break;

      default:
        return "N/A";
    }
  }

  // use function to replace test scores with percentages
  var replaceTestData = function(value, totalQs, totalCAs) {

    if (typeof value === 'undefined') {

      return "N/A";
    }

    if (totalCAs !== "") {

      return Math.round(value / (totalQs - totalCAs) * 100) + "%";
    }

    return Math.round(value / totalQs * 100) + "%";
  }

  if (usage === "question") {

    // insert html table
    $('#student-question-table-container').html(
      "<table>" +
        "<thead>" +
          "<tr>" +
            "<th>Student Name</th>" +
            "<th>Understood<br>Question</th>" +
            "<th>Correct<br>Answer</th>" +
            "<th>Understood<br>Feedback</th>" +
          "</tr>" +
        "</thead>" +
        "<tbody id=\"question-table-body\">" +
        "</tbody>" +
      "</table>"
    );

    // loop through response; add table row per entry
    for (var student in data) {
      $('#question-table-body').append(
        "<tr>" +
          "<td>" + data[student]["name"] + "</td>" +
          "<td>" + replaceQuestionData(data[student]["uq"]) + "</td>" +
          "<td>" + replaceQuestionData(data[student]["ca"]) + "</td>" +
          "<td>" + replaceQuestionData(data[student]["uf"]) + "</td>" +
        "</tr>"
      );
    }

  } else if (usage === "test") {

    // insert html table
    $('#student-question-table-container').html(
      "<table>" +
        "<thead>" +
          "<tr>" +
            "<th>Student Name</th>" +
            "<th>Questions<br>understood (%)</th>" +
            "<th>Correct<br>answers (%)</th>" +
            "<th>Feedback<br>understood (%)</th>" +
          "</tr>" +
        "</thead>" +
        "<tbody id=\"question-table-body\">" +
        "</tbody>" +
      "</table>"
    );

    // loop through response; add table row per entry
    for (var student in data.userData) {
      $('#question-table-body').append(
        "<tr>" +
          "<td>" + data.userData[student]["name"] + "</td>" +
          "<td>" + replaceTestData(
            data.userData[student]["uq"],
            data.testData["totalQuestions"],
            ""
          ) + "</td>" +
          "<td>" + replaceTestData(
            data.userData[student]["ca"],
            data.testData["totalQuestions"],
            ""
          ) + "</td>" +
          "<td>" + replaceTestData(
            data.userData[student]["uf"],
            data.testData["totalQuestions"],
            data.userData[student]["ca"]
          ) + "</td>" +
        "</tr>"
      );
    }

  } else {

    throw new Exception("Unrecognised usage.");
  }
}
