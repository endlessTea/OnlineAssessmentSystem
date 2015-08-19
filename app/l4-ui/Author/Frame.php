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
    <div id="exit-author-platform" onclick="exitPlatform();">X</div>
    <div id="clear-buffer"></div>
  </header>

  <main>

    <div class="author-level">
      <div class="author-question-description">
        <p>CREATE NEW<br>QUESTION:</p>
      </div>
      <div class="author-question-container">
        <div class="author-control" onclick="getQuestionTemplate('boolean');">
          <p>BOOLEAN</p>
        </div>
        <div class="author-control" onclick="getQuestionTemplate('multiple');">
          <p>MULTIPLE CHOICE</p>
        </div>
      </div>
      <div class="author-question-container">
        <div class="author-control" onclick="getQuestionTemplate('pattern');">
          <p>REGEX PATTERN</p>
        </div>
        <div class="author-control" onclick="getQuestionTemplate('short');">
          <p>SHORT ANSWER</p>
        </div>
      </div>
      <div class="author-control" onclick="manageQuestions();">
        <p>MANAGE<br>QUESTIONS</p>
      </div>
      <div class="author-control" onclick="loadQuestionsForTestCreation();">
        <p>CREATE<br>NEW TEST</p>
      </div>
      <div class="author-control" onclick="manageTests();">
        <p>MANAGE<br>TESTS</p>
      </div>
      <div class="author-control" onclick="loadTests();">
        <p>ISSUE<br>TEST</p>
      </div>
    </div>

    <div id="authorContainer">
      <p>Select one of the menu options above to begin.</p>
    </div>

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
