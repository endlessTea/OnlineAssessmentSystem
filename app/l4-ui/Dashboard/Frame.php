<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>

  <!-- CSS: use core stylesheet and additional dashboard styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/dashboardStyle.css">

</head>
<body>

  <header>
    <ul>
      <?php
        if ($accountType === "assessor") {
      ?>
      <li><a href="<?= URL; ?>author">Authoring</a></li>
      <li>Toggle Reports</li>
      <?php
        } else {
      ?>
      <li><a href="<?= URL; ?>assess">Assessment</a></li>
      <?php
        }
      ?>
      <li><a href="<?= URL; ?>dashboard/logout">Logout</a></li>
    </ul>
    <div id="advancedOptions"></div>
  </header>

  <main>

    <p>Welcome <?= $fullName; ?></p>

    <?php
      if ($accountType === "assessor") {
    ?>

    <div id="visualisations"></div>

    <?php
      } else {
    ?>



    <?php
      }
    ?>

  </main>

  <footer>

    <!-- JavaScript: jQuery -->
    <script src="<?= URL; ?>public/js/libs/jquery-1.11.3.min.js" charset="utf-8"></script>

    <?php
      // if user is an assessor, load additional JavaScript for D3.js visualisations
      if ($accountType === "assessor") {
    ?>
    <script src="<?= URL; ?>public/js/libs/d3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/dashboard.js" charset="utf-8"></script>

    <!-- Define base URL for JavaScript to send Ajax requests -->
    <script>
      var baseURL = '<?= URL; ?>';
    </script>
    <?php
      }
    ?>

  </footer>

</body>
</html>
