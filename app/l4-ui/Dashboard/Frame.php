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
    <h1>Dashboard</h1>
    <p>Welcome, <?= $fullName; ?></p>
  </header>

  <main>

    <ul>
      <li><a href="<?= URL; ?>author">Authoring</a></li>
      <li><a href="<?= URL; ?>assess">Assessment</a></li>
      <li><a href="<?= URL; ?>dashboard/logout">Logout</a></li>
    </ul>

    <div id="visualisations"></div>

  </main>

  <footer>

    <!-- JavaScript: jQuery, D3.js visualisation library and custom JS for Ajax/event handling -->
    <script src="<?= URL; ?>public/js/libs/jquery-1.11.3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/libs/d3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/dashboard.js" charset="utf-8"></script>

    <!-- Define base URL for JavaScript to send Ajax requests -->
    <script>
      var baseURL = '<?= URL; ?>';
    </script>

  </footer>

</body>
</html>
