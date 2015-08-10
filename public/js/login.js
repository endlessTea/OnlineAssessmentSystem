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
        $("#NotificationsFromServer").html(
          "<p>The username or password provided was not valid<br>" +
          "Please try again.</p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#NotificationsFromServer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *  GET REGISTRATION FORM
 *  Send a request for HTML template, change fields
 */
function getRegistrationForm() {

  $.ajax({
    url: baseURL + "login/getRegistrationForm",
    type: "GET",
    dataType: "html",
    success: function (response) {

      // update page details
      $('h1').html("Register New User");
      $('#prompt').html("Please provide new user details below");
      $('#UIForm').html(response);

    },
    error: function (request, status, error) {
      $("#NotificationsFromServer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}

/**
 *
 *
 */
function registerNewUser() {

  $.ajax({
    url: baseURL + "login/registerNewUser",
    data: {
      u: $('#username').val(),
      p: $('#password').val(),
      n: $('#fullname').val(),
      at: $('form input:checked').length === 1
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      if (response === "userRegistered") {
        $('#UIForm').html('');
        $("#NotificationsFromServer").html(
          "<p>The new user has been registered<br>" +
          "Please " +
          "<a href=\"" + baseURL + "\">log in</a> " +
          "with the details provided</p>"
        );
      } else if (response === "invalid") {
        $("#NotificationsFromServer").html(
          "<p>The username or password provided was not valid<br>" +
          "Please try again.</p>"
        );
      }
    },
    error: function (request, status, error) {
      $("#NotificationsFromServer").html(
        "<p>There was a problem with the request, please contact the system administrator: <br>" +
        request.responseText + "</p>"
      );
    }
  });
}
