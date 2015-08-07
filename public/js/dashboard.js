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
});
