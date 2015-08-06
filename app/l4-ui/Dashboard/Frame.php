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
  </header>

  <main>

    <p>Main content for the dashboard goes here</p>

    <div id="dashboardContainer"></div>

  </main>

  <footer>
    <!-- JavaScript: D3.js visualisation library and custom JS for Ajax/event handling -->
    <script src="<?= URL; ?>public/libs/js/d3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/dashboard.js" charset="utf-8"></script>
  </footer>

</body>
</html>
