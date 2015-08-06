/**
 *  DASHBOARD.JS
 *  @author Jonathan Lamb
 */

/**
 *  AJAX TEST
 *  @return ...?
 */
function ajaxText() {

  $.ajax({
    url: baseURL + "dashboard/ajaxTest",
    data: {
      // none
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      $("#dashboardContainer").html(response);
    },
    error: function (request, status, error) {
      alert(request.responseText);
    }
  });
}
