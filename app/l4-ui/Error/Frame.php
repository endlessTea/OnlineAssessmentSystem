<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Error</title>

  <!-- CSS: use core stylesheet and additional error page styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/errorStyle.css">

</head>
<body>

  <main>

    <?php
      // check if the request was for a forbidden page (403)
      if (isset($forbidden)) {
    ?>
    <div id="forbidden-page-image"></div>
    <div id="error-text">
      <h1>FORBIDDEN</h1>
      <p>You do not have the right account type to access this part of the application.<br>
      If you believe that this message is received in error, please contact the system administrator.</p>
    </div>
    <?php
      // otherwise the page was not found (404)
      } else {
    ?>
    <div id="error-image"></div>
    <div id="error-text">
      <h1>ERROR</h1>
      <p>The page requested was not found on the server.</p>
    </div>
    <?php
      }
    ?>

  </main>

</body>
</html>
