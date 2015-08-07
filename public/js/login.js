/**
 *  LOGIN.JS
 *  @author Jonathan Lamb
 */

/**
 *  ATTEMPT USER LOGIN
 *  Send user credentials via Ajax; redirect on success, otherwise advise credentials invalid
 */
function logUserIn() {

  $.ajax({
    url: baseURL + "login/logUserIn",
    data: {
      u: $('#username').val(),
      p: $('#password').val()
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      if (response === "sessionSet") {
        window.location.replace(baseURL);
      } else if (response === "invalid") {
        $("#loginUpdates").html(
          "<p>The username or password provided was not valid<br>" +
          "Please try again.</p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#loginUpdates").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}
