<html>
  <head>
    <title>QGen - Question Paper Generator</title>
  </head>
  <body class="bg-white font-Courier New">
    <div id="main-content">
      <?php include 'navBar.php'; ?>
      <?php include 'content.php'; ?>
    </div>
    <script>
      document.getElementById("show-tabs-button").addEventListener("click", function () {
        window.location.href="createPaper.php";
      });
      document.addEventListener("DOMContentLoaded", async function() {
        document.cookie = `section=0; path=/; max-age=3600`;
        document.cookie = `qpool_id=0; path=/; max-age=3600`;
      });
    </script>
  </body>
</html>