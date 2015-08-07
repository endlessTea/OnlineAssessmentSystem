<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Authoring Platform</title>

  <!-- CSS: use core stylesheet and authoring platform dashboard styles -->
  <link rel="stylesheet" href="<?= URL; ?>public/css/_mainStyle.css">
  <link rel="stylesheet" href="<?= URL; ?>public/css/authorStyle.css">

</head>
<body>

  <header>
    <h1>Authoring Platform</h1>
  </header>

  <main>

    <!-- refactor this -->
    <p>
      Create a new <button onclick="getQuestionTemplate('boolean');">BOOLEAN</button> question,
      <button onclick="manageQuestions();">MANAGE</button> existing questions,
      create a <button onclick="loadQuestionsForTestCreation();">NEW TEST</button>,
      <button  onclick="getTests();">MANAGE</button> existing tests or
      <button>ISSUE</button> a test to another user.
      <button onclick="exitPlatform();">EXIT</button> when you are finished.
    </p>

    <div id="authorContainer"></div>

  </main>

  <footer>

    <!-- JavaScript: jQuery and custom JS for Ajax/event handling -->
    <script src="<?= URL; ?>public/js/libs/jquery-1.11.3.min.js" charset="utf-8"></script>
    <script src="<?= URL; ?>public/js/author.js" charset="utf-8"></script>

    <!-- Define base URL for JavaScript to send Ajax requests -->
    <script>
      var baseURL = '<?= URL; ?>';
    </script>

  </footer>

</body>
</html>
