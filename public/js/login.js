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

  var accountType;
  if ($('form input:checked').length > 0) {
    accountType = "assessor";
  } else {
    accountType = "student";
  }

  $.ajax({
    url: baseURL + "login/registerNewUser",
    data: {
      u: $('#username').val(),
      p: $('#password').val(),
      n: $('#fullname').val(),
      at: accountType
    },
    type: "POST",
    dataType: "html",
    success: function (response) {
      if (response === "userRegistered") {
        $('#UIForm').html('');
        $("#NotificationsFromServer").html(
          "<p>The new user has been registered<br>" +
          "Please &nbsp;   " +
          "<a href=\"" + baseURL + "\">LOG IN</a> " +
          "    &nbsp;with the details provided</p>"
        );
      } else if (response === "taken") {
        $("#NotificationsFromServer").html(
          "<p>The username provided has already been taken.<br>" +
          "Please try again.</p>"
        );
      } else {
        $("#NotificationsFromServer").html(
          "<p>The credentials provided were not valid<br>" +
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
