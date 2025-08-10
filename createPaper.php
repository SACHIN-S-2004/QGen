<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div id="main-content">
            <?php include 'navBar.php'; ?>
            <div class="max-w-7xl px-4 w-75 py-10 mt-[100px] ml-[200px] me-4 bg-light rounded-1">
                <div id="create-work" > 
                    <h2 class="text-3xl font-bold text-center text-gray-900 mb-4">Create Question Pool</h2><br>
                    <div id="questionPoolContainer" class="d-flex justify-content-between align-items-center border p-2 ml-4">
                        <input type="text" id="poolName" class="form-control me-2" placeholder="Enter Question Pool Name">
                        <button class="btn btn-primary" id="show-tabs-button">Create</button>
                    </div>
                </div>
                <pre class="mt-4 ml-[50px] text-gray-700">
Recommended Format:
	Batch Name_Exam Name_Subject Name 
	Eg : B22-26_Internal-2_Python
</pre>
            </div>
        <div>
    </body>
</html>

<div class="modal fade" id="qpDescModal" tabindex="-1" aria-labelledby="qpDescModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <?php include 'metadata.html'; ?>
    </div>
  </div>
</div>

<script>
    document.getElementById("show-tabs-button").addEventListener("click", function () {
        openPage();
    });
    function openPage() {
        //window.location.href = "login.php";
        let poolName = document.getElementById('poolName').value.trim();
        if (poolName === "") {
            alert("Please enter a name for the question pool.");
            return;
        }

        Cookies.set('poolName', poolName, { expires: 365 });
        event.preventDefault();
        var myModal = new bootstrap.Modal(document.getElementById('qpDescModal'));
        myModal.show();
        //console.log("Cookie is not set.");
        //appearTab();
    }
</script>