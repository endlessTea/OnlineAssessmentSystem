/**
 *  DASHBOARD.JS
 *  @author Jonathan Lamb
 */

/**
 *  WIREFRAME VISUALISATIONS
 */
$(function() {

  $('#visualisations').html(
    "<div id=\"visLeft\" class=\"floatBox\"></div><div id=\"visRight\" class=\"floatBox\"></div>"
  ).css({
    'display' : '-webkit-flex',
    '-webkit-flex-direction' : 'row',
    'display' : 'flex',
    'flex-direction' : 'row'
  });
  $('#visLeft').css({
    'border' : '2px dashed #000',
    'max-width' : '400px',
    'min-width' : '400px',
    'height' : '400px'
  });
  $('#visRight').css({
    'border' : '2px dashed #000',
    'max-width' : '400px',
    'min-width' : '400px',
    'height' : '400px'
  });
  $('.floatBox').css({
    '-webkit-flex' : '1 1 auto',
    'flex' : '1 1 auto',
    'margin' : 'auto'
  });

  drawScatterplot();
  drawArcCorners();
});

/**
 *  DRAW EXAMPLE VISUALISATIONS
 */
var drawScatterplot = function() {

  // http://bl.ocks.org/mbostock/3887118
  var margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = 400 - margin.left - margin.right,
    height = 400 - margin.top - margin.bottom;

  var x = d3.scale.linear()
    .range([0, width]);

  var y = d3.scale.linear()
    .range([height, 0]);

  var color = d3.scale.category10();

  var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

  var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

  var svg = d3.select("#visLeft").append("svg")
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

    svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis)
      .append("text")
        .attr("class", "label")
        .attr("x", width)
        .attr("y", -6)
        .style("text-anchor", "end")
        .text("User Id");

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

/**
 *  DRAW ARC CORNERS
 */
var drawArcCorners = function() {

  // http://bl.ocks.org/mbostock/c501f6cae402ab5e90c9
  var width = 400,
    height = 400,
    radius = height / 2 - 10;

  var arc = d3.svg.arc()
    .innerRadius(radius - 80)
    .outerRadius(radius)
    .cornerRadius(20);

  var pie = d3.layout.pie()
    .padAngle(.03);

  var color = d3.scale.category10();

  var svg = d3.select("#visRight").append("svg")
    .attr("width", width)
    .attr("height", height)
    .append("g")
      .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

  d3.json("public/js/donut_ex1.json", function(error, data) {
    if (error) throw error;

    // refactor this?
    newData = [0, 0, 0, 0, 0, 0, 0, 0];
    data.forEach(function(row) {
      if (row.uq == 1) {
        if (row.ca == 1) {
          if (row.uf == 1) {
            newData[0]++;
          } else {
            newData[1]++;
          }
        } else {
          if (row.uf == 1) {
            newData[2]++;
          } else {
            newData[4]++;
          }
        }
      } else {
        if (row.ca == 1) {
          if (data.uf == 1) {
            newData[3]++;
          } else {
            newData[6]++;
          }
        } else {
          if (row.uf == 1) {
            newData[5]++;
          } else {
            newData[7]++;
          }
        }
      }
    });

    // http://bl.ocks.org/mbostock/3887193
    var g = svg.selectAll(".arc")
      .data(pie(newData))
      .enter().append("g")
        .attr("class", "arc");

    g.append("path")
        .style("fill", function(d, i) { return color(i); })
        .attr("d", arc);

    g.append("text")
      .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")" })
      .attr("dy", ".35em")
      .style("text-anchor", "middle")
      .text(function(d) { return (d.data / 20 * 100) + "%"; });

  });
}

/**
 *  DRAW DONUT
 *  Making inactive (vis_3)

var drawDonut = function() {

  // http://bl.ocks.org/mbostock/32bd93b1cc0fbccc9bf9
  var width = 400,
    height = 400;

  var outerRadius = height / 2 - 20,
    innerRadius = outerRadius / 3,
    cornerRadius = 10;

  var pie = d3.layout.pie()
    .padAngle(.02);

  var arc = d3.svg.arc()
    .padRadius(outerRadius)
    .innerRadius(innerRadius);

  d3.json("public/js/donut_ex1.json", function(error, data) {
    if (error) throw error;

    // refactor this?
    newData = [0, 0, 0, 0, 0, 0, 0, 0];
    data.forEach(function(row) {
      if (row.uq == 1) {
        if (row.ca == 1) {
          if (row.uf == 1) {
            newData[0]++;
          } else {
            newData[1]++;
          }
        } else {
          if (row.uf == 1) {
            newData[2]++;
          } else {
            newData[4]++;
          }
        }
      } else {
        if (row.ca == 1) {
          if (data.uf == 1) {
            newData[3]++;
          } else {
            newData[6]++;
          }
        } else {
          if (row.uf == 1) {
            newData[5]++;
          } else {
            newData[7]++;
          }
        }
      }
    });

    var svg = d3.select("#visRight").append("svg")
      .attr("width", width)
      .attr("height", height)
      .append("g")
        .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    svg.selectAll("path")
      .data(pie(newData))
      .enter().append("path")
        .each(function(d) {
          d.outerRadius = outerRadius - 20;
        })
        .attr("d", arc)
        .on("mouseover", arcTween(outerRadius, 0))
        .on("mouseout", arcTween(outerRadius - 20, 150));

    function arcTween(outerRadius, delay) {
      return function() {
        // console.log(this);
        d3.select(this).transition().delay(delay).attrTween("d", function(d) {
          var i = d3.interpolate(d.outerRadius, outerRadius);
          return function(t) { d.outerRadius = i(t); return arc(d); };
        });
      };
    }
  });
}
 */
