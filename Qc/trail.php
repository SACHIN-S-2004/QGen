<?php
    session_start();

    $connect = new mysqli("localhost", "root", "", "qgen1");
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $isViewMode = isset($_GET['mode']) && $_GET['mode'] === 'view';
    $questionData = [];
    $subQuestions = [];
    $imageData = null;

    // Fetch tchapter from qpool table
    $qpool_id = isset($_COOKIE['qpool_id']) ? $_COOKIE['qpool_id'] : 1; // Default to 1 if not set
    $tchapterQuery = "SELECT tchapter FROM qpool WHERE qpool_id = $qpool_id";
    $tchapterResult = $connect->query($tchapterQuery);
    $totalChapters = ($tchapterResult && $tchapterResult->num_rows > 0) ? (int)$tchapterResult->fetch_assoc()['tchapter'] : 1;

    if ($isViewMode && isset($_GET['qID'])) {
        $qID = (int)$_GET['qID'];
        $_SESSION['qID']=$qID;
        setcookie('qID', $qID, time() + (86400 * 3), "/"); // Cookie valid for 30 days
        $query = "SELECT qpool_id, content, QType, section, mark, chapter, difficulty FROM question WHERE Q_id = $qID";
        $result = $connect->query($query);
        if ($result->num_rows > 0) {
            $questionData = $result->fetch_assoc();
        }

        if ($questionData['QType'] === 'sub') {
            $query = "SELECT content FROM SQuestion WHERE Q_id = $qID";
            $result = $connect->query($query);
            while ($row = $result->fetch_assoc()) {
                $subQuestions[] = $row['content'];
            }
        } elseif ($questionData['QType'] === 'img') {
            $query = "SELECT picture FROM PQuestion WHERE Q_id = $qID";
            $result = $connect->query($query);
            if ($result->num_rows > 0) {
                $imageData = $result->fetch_assoc()['picture'];
            }
        }
    }

// Handle POST for editing (if submitted)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_submit'])) {
        //$qID = (int)$_POST['qID'];
        $qID=$_SESSION['qID'];

        $question = $_POST['question'];
        $q_type = $_POST['question_type'];
        $difficulty = $_POST['difficulty'];
        $section = $_POST['section'];
        $chapter = $_POST['chapter'];
        $marks = $_POST['marks'];

        $query = "UPDATE question SET content='$question', QType='$q_type', difficulty='$difficulty', section='$section', chapter='$chapter', mark='$marks' WHERE Q_id='$qID'";
        if ($connect->query($query)) {
            if ($q_type === 'sub') {
                $connect->query("DELETE FROM SQuestion WHERE Q_id=$qID");
                for ($i = 1; $i <= 5; $i++) {
                    if (isset($_POST["Sub-Question_$i"]) && !empty($_POST["Sub-Question_$i"])) {
                        $val = $_POST["Sub-Question_$i"];
                        $connect->query("INSERT INTO SQuestion (qpool_id, Q_id, content) VALUES (5, $qID, '$val')");
                    }
                }
            } elseif ($q_type === 'img' && isset($_FILES['attachFile']['tmp_name'])) {
                $imageData = addslashes(file_get_contents($_FILES['attachFile']['tmp_name']));
                $connect->query("UPDATE PQuestion SET picture='$imageData' WHERE Q_id=$qID");
            }
            //echo "<script>updateWordCount();</script>";
            echo "<script>window.parent.postMessage('closeIframe', '*');</script>";
        } else {
            echo "Error updating question";
        }
    }
    elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        /*echo '<pre>';
        print_r($_POST);
        echo '</pre>';*/
        //echo "Question".$question. "<br>";
        $question = $_POST['question'];
        $q_type = $_POST['question_type'];
        $difficulty = $_POST['difficulty'];
        $section = $_POST['section'];
        $chapter = $_POST['chapter'];
        $marks = $_POST['marks'];

        $qpool_id=$_COOKIE['qpool_id'];
        //$qpool_id=5;
        $query = "CREATE table if not exists question(qpool_id INT,Q_id INT AUTO_INCREMENT PRIMARY KEY,content TEXT NOT NULL,QType varchar(10),difficulty varchar(10),section varchar(5),mark INT)";
        if(!($Result = $connect->query($query))){
            echo "Error,Table is not Created";
        }

        $query = "insert into question(qpool_id,content,QType,difficulty,section,chapter,mark) values('$qpool_id','$question','$q_type','$difficulty','$section','$chapter','$marks')";

        if(!($Result = $connect->query($query))){
            echo "Error";
        }else{
            echo "NData Added successfully!";
        }
        $Q_id=$connect->insert_id;

        if($q_type=="sub"){
            $query = "create table if not exists SQuestion(qpool_id INT,Q_id INT,content TEXT NOT NULL)";
            if(!($Result = $connect->query($query))){
                echo "Error,Table is not Created";
            } 

            for ($i = 1; $i <= 5; $i++) { 
                if (isset($_POST["Sub-Question_" . $i])) {
                    $val = $_POST["Sub-Question_" . $i];
                    /*echo "Sub-Question $i: " . htmlspecialchars($val) . "<br>";*/
                    //error_log("Sub-Question $i: " . htmlspecialchars($val));
                    $query = "insert into SQuestion(qpool_id,Q_id,content) values('$qpool_id','$Q_id','$val')";
                    if(!($Result = $connect->query($query))){
                        echo "Error";
                    }else{
                        echo "SData Added successfully!";
                    }
                }
            }

        }
        elseif($q_type=="img"){
            $query = "create table if not exists PQuestion(qpool_id INT,Q_id INT,picture LONGBLOB)";
            if(!($Result = $connect->query($query))){
                echo "Error,Table is not Created";
            }

            $imageData = addslashes(file_get_contents($_FILES['attachFile']['tmp_name']));

            $query = "insert into PQuestion values('$qpool_id','$Q_id','$imageData')";
            if(!($Result = $connect->query($query))){
                echo "Error";
            }else{
                echo "PData Added successfully!";
            }
        }

        /*header("Location: " . $_SERVER['PHP_SELF']);
        exit();*/
        
        echo "<script>
        function closePage() {
                window.parent.postMessage('closeIframe', '*');
        }
        closePage();
        </script>";
        $connect->close();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Question Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /*.container {
            width:1200px;
        }*/
        .disabled-section {
            opacity: 0.5;
            pointer-events: none;
        }
        .form-section {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
        }
        .form-section {
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
        }
        .btn-group {
            position: relative;
        }
        .btn {
            position: relative;
        }
        .btn.active {
            background-color: var(--bs-btn-hover-bg) !important;
            border-color: var(--bs-btn-hover-border-color) !important;
            color: white !important;
        }
        .btn.active::after {
            content: "✔"; /* Unicode checkmark */
            font-size: 12px;
            color: rgb(250, 250, 250) !important; 
            position: absolute;
            top: -5px;
            right: -5px;
            background-color:rgb(47, 244, 93);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
            /*font-weight: bold;
            align-items: center;
            justify-content: center;*/
        }
        .sticky-header {
            position: sticky;
            /*top: 0;
            background: white;
            z-index: 1000;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);*/
            margin-bottom: 2px;
            flex-direction: row;
            justify-content: flex-end;
        }
    </style>
    <script>
        function enableEdit() {
            document.getElementById('question-details').classList.remove('disabled-section');
            document.getElementById('questionDiv').classList.remove('disabled-section');
            document.querySelectorAll('#question-details input, #question-details select, #question-details button').forEach(el => el.removeAttribute('disabled'));
        }
       function updateWordCount(){
        var wordCount = document.getElementById('word_count');
        var charCount = document.getElementById('char_count');
        var question = document.getElementById('question').value.trim();
        var words = question.split(/\s+/).filter(function(word) {
            return word.length > 0;
        }).length;

        var chars = question.length;
        wordCount.innerText = words;
        charCount.innerText = chars;
       }
       function clrWordCount(){
        var wordCount = document.getElementById('word_count');
        var charCount = document.getElementById('char_count');
        wordCount.innerText = 0;
        charCount.innerText = 0;
       }
       function setActive(button) {
            document.querySelectorAll(".btn-group .btn").forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");
            let difficulty = button.textContent.trim(); // Get the text of the clicked button
            document.getElementById("difficulty").value = difficulty; // Set it to the hidden input field
        }
        document.addEventListener("DOMContentLoaded", function() {
            let maxSubQuestions = 5;
            let dynamicContainer = document.getElementById("dynamicInputContainer");
            let questionTextarea = document.getElementById("question");
            let questionType = document.getElementById("questionType");

            questionType.addEventListener("change", function() {
                dynamicContainer.innerHTML = ""; // Clear previous inputs
                if (this.value === "sub") {
                    questionTextarea.rows = 4;
                    let subQuestionContainer = document.createElement("div");
                    subQuestionContainer.id = "subQuestionContainer";
                    dynamicContainer.appendChild(subQuestionContainer);
                    for (let i = 1; i <= 3; i++) {
                        addSubQuestion(i);
                    }
                    addControlButtons();
                } else if (this.value === "img") {
                    questionTextarea.rows = 7;
                    let fileInput = document.createElement("input");
                    fileInput.type = "file";
                    fileInput.className = "form-control mt-2";
                    fileInput.name = "attachFile";
                    dynamicContainer.appendChild(fileInput);
                } else {
                    questionTextarea.rows = 9;
                }
            });

            function addSubQuestion(index) {
                let subQuestionContainer = document.getElementById("subQuestionContainer");
                let subQuestionInput = document.createElement("input");
                subQuestionInput.type = "text";
                subQuestionInput.className = "form-control mt-2";
                subQuestionInput.name = "Sub-Question_"+ index;
                subQuestionInput.placeholder = "Sub-Question " + index;
                subQuestionContainer.appendChild(subQuestionInput);
            }

            function addControlButtons() {
                let subQuestionContainer = document.getElementById("subQuestionContainer");
                let addBtn = document.createElement("button");
                addBtn.type = "button";
                addBtn.className = "btn btn-outline-primary mt-2 me-2";
                addBtn.textContent = "+ Add Sub-Question";
                addBtn.onclick = function() {
                    let count = subQuestionContainer.querySelectorAll("input[type='text']").length;
                    if (count < maxSubQuestions) {
                        addSubQuestion(count + 1);
                    } else {
                        alert("Only " + maxSubQuestions + " sub-questions allowed!");
                    }
                };

                let removeBtn = document.createElement("button");
                removeBtn.type = "button";
                removeBtn.className = "btn btn-outline-danger mt-2";
                removeBtn.textContent = "- Remove Last";
                removeBtn.onclick = function() {
                    let inputs = subQuestionContainer.querySelectorAll("input[type='text']");
                    if (inputs.length > 1) {
                        subQuestionContainer.removeChild(inputs[inputs.length - 1]);
                    } else if (inputs.length === 1) {
                        alert("A minimum of one sub-question is needed.");
                    }
                };

                dynamicContainer.appendChild(addBtn);
                dynamicContainer.appendChild(removeBtn);
            }
        });
        function closePage() {
            //window.close();
            //window.frameElement.remove();
            //window.location.href="questionC.php";
            window.parent.postMessage("closeIframe", "*");
            
        }
        function deleteQ() {
            /*if (confirm("Are you sure you want to delete this question?")) {
                window.parent.postMessage("deleteQuestion", "*");
            }*/
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("deleteQ.php?data=qDelete", {
                        method: "POST"
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("Response:", data);                                                          
                        
                        if (data.trim() === "success") {
                            window.parent.postMessage("closeIframe", "*");
                        } else {
                            //errorDiv.style.display = 'block';
                            //errorDiv.innerHTML = `${data}`;
                            console.log( `${data}`);
                        }
                    })
                    .catch(error => {
                        console.error("Fetch Error:", error);
                        //errorDiv.style.display = 'block';
                        //errorDiv.innerHTML = 'An error occurred. Please try again.';
                    });
                }
            });
        }
    </script>
</head>
<body class="bg-light">
    <div class="container flex" style="padding:5px; ">
            <div class="sticky-header d-flex">
                <?php if ($isViewMode): ?>
                    <button onclick="enableEdit()" class="btn btn-warning float-end m-3">Edit</button>
                    <button onclick="deleteQ()" class="btn btn-danger float-end m-3">Delete</button>
                <?php endif; ?>
                <button onclick="closePage()" class="btn-close float-end ms-4 m-3 mt-3" aria-label="Close"></button>
            </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <?php if ($isViewMode): ?>
                <input type="hidden" name="qID" value="<?php echo isset($_GET['qID']) ? $_GET['qID'] : ''; ?>">
                <input type="hidden" name="edit_submit" value="1">
            <?php endif; ?>
            <div class="row g-3" style="display: flex; flex-wrap: nowrap;">
                <div class="col-md-7 form-section <?php echo $isViewMode ? 'disabled-section' : ''; ?> opacity-100" id="questionDiv" style="margin-right: 15px;">
                    <h2 class="mb-4"><?php echo $isViewMode ? 'View/Edit Question' : 'Submit a Question'; ?></h2>
                    <div class="mb-3">
                        <label for="question" class="form-label">Question</label>
                        <textarea name="question" id="question" class="form-control" rows="9" placeholder="Enter your question here..." oninput="updateWordCount()"><?php echo $isViewMode ? htmlspecialchars($questionData['content']) : ''; ?></textarea>                      
                    </div>
                    <div id="dynamicInputContainer" class="mb-3" style="max-height: 200px; overflow-y: auto;">
                        <?php
                        if ($isViewMode && $questionData['QType'] === 'sub') {
                            foreach ($subQuestions as $i => $subQ) {
                                echo "<input type='text' class='form-control mt-2' name='Sub-Question_" . ($i + 1) . "' value='" . htmlspecialchars($subQ) . "'>";
                            }
                        } elseif ($isViewMode && $questionData['QType'] === 'img' && $imageData) {
                            echo "<img src='data:image/jpeg;base64," . base64_encode($imageData) . "' class='img-fluid mt-2' style='max-height:200px;'>";
                            echo "<input type='file' class='form-control mt-2' name='attachFile'>";
                        }
                        ?>
                    </div>
                    <p>Words: <span id="word_count">0</span> | Characters: <span id="char_count">0</span></p>
                    <?php 
                        if ($isViewMode): 
                            echo "<script>updateWordCount();</script>";
                        endif; 
                    ?>
                    <?php if ($isViewMode): ?>
                        <div class="button-container mt-4">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    <?php else: ?>
                        <div class="button-container mt-4">
                            <button type="reset" class="btn btn-danger" onclick="clrWordCount()">Clear Form</button>
                            <button type="submit" class="btn btn-success">Save Question</button>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-5 form-section <?php echo $isViewMode ? 'disabled-section' : ''; ?>" id="question-details">
                    <h4 class="mb-4">Question Details</h4>
                    <div class="mb-3">
                        <label for="questionType" class="form-label">Question Type</label>
                        <select id="questionType" name="question_type" class="form-select" <?php echo $isViewMode ? 'disabled' : ''; ?>>
                            <?php if (!($isViewMode)): ?>
                                <option selected hidden>Choose...</option>
                            <?php endif; ?>
                            <option value="plain" <?php echo $isViewMode && $questionData['QType'] == 'plain' ? 'selected' : ''; ?>>Plain Question</option>
                            <option value="sub" <?php echo $isViewMode && $questionData['QType'] == 'sub' ? 'selected' : ''; ?>>Sub-Question</option>
                            <option value="img" <?php echo $isViewMode && $questionData['QType'] == 'img' ? 'selected' : ''; ?>>Question with Picture</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="mb-3">
                            <label for="section" class="form-label">Section</label>
                            <select name="section" id="section" class="form-select" <?php echo $isViewMode ? 'disabled' : ''; ?>>
                                <?php if (!($isViewMode)): ?>
                                    <option selected hidden>Choose...</option>
                                <?php endif; ?>
                                <?php
                                // Get the section value from the cookie, default to 1 if not set
                                $sectionCount = isset($_COOKIE['section']) ? (int)$_COOKIE['section'] : 1;

                                // Array of possible sections
                                $sections = ['A' => 'Part A', 'B' => 'Part B', 'C' => 'Part C', 'D' => 'Part D','E' => 'Part E'];

                                // Loop through sections based on cookie value
                                for ($i = 0; $i < $sectionCount && $i < 5; $i++) {
                                    $sectionKey = array_keys($sections)[$i];
                                    $sectionLabel = $sections[$sectionKey];
                                    $isSelected = ($isViewMode && isset($questionData['section']) && $questionData['section'] == $sectionKey) ? 'selected' : '';
                                    echo "<option value=\"$sectionKey\" $isSelected>$sectionLabel</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="difficulty" id="difficulty" value="<?php echo $isViewMode ? $questionData['difficulty'] : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="chapter" class="form-label">Chapter</label>
                        <select name="chapter" id="chapter" class="form-select" <?php echo $isViewMode ? 'disabled' : ''; ?>>
                            <?php if (!($isViewMode)): ?>
                                <option selected hidden>Choose...</option>
                            <?php endif; ?>
                            <?php
                            for ($i = 1; $i <= $totalChapters; $i++) {
                                $isSelected = ($isViewMode && isset($questionData['chapter']) && $questionData['chapter'] == $i) ? 'selected' : '';
                                echo "<option value=\"$i\" $isSelected>Chapter $i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="marks" class="form-label">Marks</label>
                        <input type="number" name="marks" id="marks" class="form-control" placeholder="Enter marks" value="<?php echo $isViewMode ? $questionData['mark'] : ''; ?>" <?php echo $isViewMode ? 'disabled' : ''; ?>>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- Bootstrap 5 JS (optional for functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
