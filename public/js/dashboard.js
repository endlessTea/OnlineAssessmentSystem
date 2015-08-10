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
    'max-width' : '300px',
    'min-width' : '300px',
    'height' : '300px'
  });
  $('#visRight').css({
    'border' : '2px dashed #000',
    'max-width' : '300px',
    'min-width' : '300px',
    'height' : '300px'
  });
  $('.floatBox').css({
    '-webkit-flex' : '1 1 auto',
    'flex' : '1 1 auto',
    'margin' : 'auto'
  });

  drawScatterplot();
  drawDonut();
});

/**
 *  DRAW EXAMPLE VISUALISATIONS
 */
var drawScatterplot = function() {

  // http://bl.ocks.org/mbostock/3887118
  var margin = {top: 5, right: 5, bottom: 7, left: 9},
    width = 300 - margin.left - margin.right,
    height = 300 - margin.top - margin.bottom;

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

var drawDonut = function() {

}
