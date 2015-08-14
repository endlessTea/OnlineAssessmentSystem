<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>

  <!-- CSS: use core stylesheet and additional dashboard styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/dashboardStyle.css">

  <?php
    if ($accountType === "assessor") {
  ?>
  <link rel="stylesheet" href="<?= URL; ?>public/css/visualisationStyle.css">
  <?php
    }
  ?>

</head>
<body>

  <header>
    <div class="dash-level">
      <?php
        if ($accountType === "assessor") {
      ?>
      <a href="<?= URL; ?>author">
        <div class="dash-control" id="author-button">
          <p>AUTHOR QUESTIONS/TESTS</p>
        </div>
      </a>
      <div class="dash-control" id="vis-toggle-button" onclick="toggleVisualisations();">
        <p>TOGGLE DATA VISUALISATIONS</p>
      </div>
      <?php
        } else {
      ?>
      <a href="<?= URL; ?>assess">
        <div class="dash-control student-button" id="assess-button">
          <p>TAKE A TEST</p>
        </div>
      </a>
      <?php
        }
      ?>
      <a href="<?= URL; ?>dashboard/logout">
        <div class="dash-control" id="logout-button">
          <p>LOGOUT</p>
        </div>
      </a>
    </div>
    <div class="dash-level" id="advancedOptions"></div>
  </header>

  <main>

    <p id="page-prompt">Welcome <?= $fullName; ?></p>

    <?php
      // provide different welcome text for assessors and students
      if ($accountType === "assessor") {
    ?>

    <!-- provide data visualisation container for assessor accounts -->
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
