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

  <header>
    <h1>Login</h1>
  </header>

  <main>

    <p>Please provide your username and password</p>

    <div id="loginUpdates"></div>

    <form onsubmit="logUserIn(); return false;">
      <div class="loginField">
        <label for="username">Username</label>
        <input type="text"
          id="username"
          autocomplete="off"
          autofocus
          pattern="[a-zA-Z0-9]+"
          required>
      </div>
      <div class="loginField">
        <label for="password">Password</label>
        <input type="password"
          id="password"
          autocomplete="off"
          pattern="[a-zA-Z0-9]+"
          required>
      </div>
      <div class="loginField">
        <input type="submit" value="Log In">
      </div>
    </div>

  </main>

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
