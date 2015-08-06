/**
 *  DASHBOARD.JS
 *  @author Jonathan Lamb
 */

/**
 *  AJAX TEST
 *  @return ...?
 */
function ajaxText() {

  //alert('hi');

  $.ajax({
    url: baseURL + "dashboard/fetchYMAFModuleSelection",
  });
}
