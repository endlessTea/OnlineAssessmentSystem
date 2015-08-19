<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Assessment Platform</title>

  <!-- CSS: use core stylesheet and additional assessment platform styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/assessStyle.css">

</head>
<body>

  <header>
    <h1>Assessment Platform</h1>
    <div id="exit-assess-platform" onclick="exitPlatform();">X</div>
  </header>

  <div id="clear-buffer"></div>

  <main>

    <div id="assessContainer">

      <?php
        if (!is_array($tests)) {
          echo "<p>" . $tests . "</p>";
        } else {
          foreach ($tests as $tId => $name) {
      ?>

      <h2><?= $name; ?>
      <button onclick="checkAndLoadDisclaimer('<?= $tId; ?>');">LOAD</button></h2>

      <?php
          }
        }
      ?>

    </div>

  </main>

  <footer>

    <!-- JavaScript: jQuery and custom JS for Ajax/event handling -->
    <script src="<?= URL; ?>public/js/libs/jquery-1.11.3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/assess.js" charset="utf-8"></script>

    <!-- Define base URL for JavaScript to send Ajax requests -->
    <script>
      var baseURL = '<?= URL; ?>';
    </script>

  </footer>

</body>
</html>
