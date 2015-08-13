<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login</title>

  <!-- CSS: use core stylesheet and authoring platform dashboard styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/loginStyle.css">

</head>
<body>

    <!-- CSS selectors adapted from 'Login Container' by 'Ayu' (see loginStyle.css) -->
    <div class="login-card">
      <h1>Log In</h1><br>
      <div id="NotificationsFromServer"></div>
      <div id="UIForm">
        <form onsubmit="logUserIn(); return false;">
          <input type="text"
            id="username"
            autocomplete="off"
            autofocus
            pattern="[a-zA-Z0-9]+"
            title="Alphanumeric characters only"
            placeholder="Username"
            required>
          <input type="password"
            id="password"
            autocomplete="off"
            pattern="[a-zA-Z0-9]+"
            title="Alphanumeric characters only"
            placeholder="Password"
            required>
          <input type="submit" class="login login-submit" value="Log In">
        </form>
        <div class="login-help">
          Or&nbsp;
          <span onclick="getRegistrationForm();">REGISTER</span>
          &nbsp;a new user
        </div>
      </div>
    </div>

  <footer>

    <!-- JavaScript: jQuery and custom JS for Ajax/event handling -->
    <script src="<?= URL; ?>public/js/libs/jquery-1.11.3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/login.js" charset="utf-8"></script>

    <!-- Define base URL for JavaScript to send Ajax requests -->
    <script>
      var baseURL = '<?= URL; ?>';
    </script>

  </footer>

</body>
</html>
