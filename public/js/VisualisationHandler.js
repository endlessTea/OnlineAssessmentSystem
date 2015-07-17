// http://bl.ocks.org/mbostock/32bd93b1cc0fbccc9bf9

var data = [1, 1, 2, 3, 5, 8, 13, 21, 34, 55, 61, 70, 71, 72];

var width = 400,
    height = 400;

var outerRadius = height / 2 - 20,
    innerRadius = outerRadius / 3,
    cornerRadius = 10;

// takes an array and turns each entry to an array of objects
var pie = d3.layout.pie()
    .padAngle(.02);

var arc = d3.svg.arc()
    .padRadius(outerRadius)
    .innerRadius(innerRadius);

// work with csv data
d3.csv(baseURL + 'public/csv/vis_sample_1.csv', function(error, rows) {
  if (error) throw error;

  /*
   *  New data
   *  0 = understands question, correct answer, understand feedback
   *  1 = understands question, correct answer, does not understand feeback
   *  2 = understands question, incorrect answer, understands feedback
   *  3 = does not understand question, correct answer, understands feedback
   *  4 = understands question, incorrect answer, does not understand feedback
   *  5 = does not understand question, incorrect answer, understands feedback
   *  6 = does not understand question, correct answer, does not understand feedback
   *  7 = does not understand question, incorrect answer, does not understand feedback
   */
  newData = [0, 0, 0, 0, 0, 0, 0, 0];
  rows.forEach(function(row) {
    if (row.understand_question == 1) {
      if (row.correct_answer == 1) {
        if (row.understand_feedback == 1) {
          newData[0]++;
        } else {
          newData[1]++;
        }
      } else {
        if (row.understand_feedback == 1) {
          newData[2]++;
        } else {
          newData[4]++;
        }
      }
    } else {
      if (row.correct_answer == 1) {
        if (row.understand_feedback == 1) {
          newData[3]++;
        } else {
          newData[6]++;
        }
      } else {
        if (row.understand_feedback == 1) {
          newData[5]++;
        } else {
          newData[7]++;
        }
      }
    }
  });

  var svg = d3.select("#sample1").append("svg")
      .attr("width", width)
      .attr("height", height)
    .append("g")
      .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

  svg.selectAll("path")
      .data(pie(newData))
    .enter().append("path")
      .each(function(d) {
        console.log((d.data / 20 * 100) + '%');   // do something with this...
        d.outerRadius = outerRadius - 20;
      })
      .attr("d", arc)
      .on("mouseover", arcTween(outerRadius, 0))
      .on("mouseout", arcTween(outerRadius - 20, 150));

  function arcTween(outerRadius, delay) {
    return function() {
      d3.select(this).transition().delay(delay).attrTween("d", function(d) {
        var i = d3.interpolate(d.outerRadius, outerRadius);
        return function(t) { d.outerRadius = i(t); return arc(d); };
      });
    };
  }
});
