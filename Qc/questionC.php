<?php
    $connect = new mysqli("localhost", "root", "", "qgen1");

    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $query = "CREATE table if not exists question(qpool_id INT,Q_id INT AUTO_INCREMENT PRIMARY KEY,content TEXT NOT NULL,QType varchar(10),difficulty varchar(10),section varchar(5),mark INT)";
    if(!($Result = $connect->query($query))){
        echo "Error,Table is not Created";
    }
?>
<html>
<head>
    <title>Question Paper Sections</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f5f5f5;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1000;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-3px);
        }
        .card-title {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .question-list {
            min-height: 50px;
        }
        .btn-primary, .btn-danger, .btn-info {
            border-radius: 20px;
            padding: 8px 20px;
        }
        .btn-info {
            color:white;
        }
        #iframe-container {
            position: fixed;
            top: 2%;
            left: 50px;
            width: 95%;
            height: 100%;
            /*background: rgba(0, 0, 0, 0.5);*/
            /*display: none;*/
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        #iframe-container iframe {
            height: 640px;
            width: auto;
            background: transparent;
            border-radius: 8px;
            border: none;
        }
        #main-content.blur {
            filter: blur(3px);
            pointer-events: none;
        }
        .view-btn {
            transition: transform 0.3s ease;
        }

        .view-btn:hover {
            transform: scale(1.1);
        }

        .view-btn1 {
            transition: transform 0.3s ease;
        }
        .view-btn1:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body class="container mt-4">
    <div id="main-content">
        <!-- Header (appears only once) -->
        <div class="sticky-header d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0">Question Paper Sections</h2>
            <div>
                <button class="btn btn-info me-2" onclick="window.location.href='../importQ/import_questions.php'">Import Questions</button>
                <button class="btn btn-primary me-2" onclick="openQuestionForm()">Create Question</button>
                <button class="btn btn-danger" onclick="window.location.href='../homepage.php'">Save and Exit</button>
            </div>
        </div>
    
        <!-- Container for dynamically created sections -->
        <div id="sections-container"></div>
    </div>

    <!-- Iframe container -->
    <div id="iframe-container" class="mt-4" style="display:none;">
        <div class="card p-3 shadow-sm">
            <iframe id="question-iframe"></iframe>
        </div>
    </div>

    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return 0; // Return `null` or any other default value
        }

        async function loadContent() {
            const { value: numSections } = await Swal.fire({
                title: "Number Of Sections",
                input: "number",
                inputPlaceholder: "Enter the number of sections",
                inputAttributes: { min: 1 },
                confirmButtonText: "Generate",
                background: '#fff',
                allowOutsideClick: false,
                confirmButtonColor: '#007bff'
            });
            
            if (numSections && numSections > 5) { 
                Swal.fire({
                    title: "Error",
                    text: "Number of sections should not exceed 5.",
                    icon: "error",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else if (numSections && numSections > 0) {
                const { value: numChapters } = await Swal.fire({
                    title: "Total Number of Chapters",
                    input: "number",
                    inputPlaceholder: "Enter the total number of chapters",
                    inputAttributes: { min: 1, max: 8 },
                    confirmButtonText: "Submit",
                    background: '#fff',
                    allowOutsideClick: false,
                    confirmButtonColor: '#007bff',
                    preConfirm: (value) => {
                        if (value < 0 || value > 8) {
                            Swal.showValidationMessage("Number of chapters must be between 0 and 8.");
                        }
                    }
                });

                if (numChapters !== undefined) { // Proceed only if chapters input is valid
                    console.log(numSections, numChapters);
                    fetch(`fetch_questions.php?data=${numSections}&chapters=${numChapters}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.statusText}`);
                            }
                            return response.text();
                        })
                        .then(data => {
                            document.cookie = `section=${numSections}; path=/; max-age=86400`; // Set cookie for 1 day
                            const container = document.getElementById("sections-container");
                            container.innerHTML = ""; // Clear previous sections
                            for (let i = 1; i <= numSections; i++) {
                                let sectionDiv = document.createElement("div");
                                sectionDiv.className = "card shadow-sm p-3 mt-3";
                                sectionDiv.innerHTML = `
                                    <div class="card-body">
                                        <h5 class="card-title">Section ${i}</h5>
                                        <div id='section-${i}' class='question-list border rounded p-2 bg-light'></div>
                                    </div>`;
                                container.appendChild(sectionDiv);
                                
                                fetch(`fetch_questions.php?section=${i}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`Network response was not ok: ${response.statusText}`);
                                        }
                                        return response.text();
                                    })
                                    .then(data => {
                                        document.getElementById(`section-${i}`).innerHTML = data;
                                    })
                                    .catch(error => console.error("Error fetching questions:", error));
                            }
                        })
                        .catch(error => console.error("Error fetching questions:", error));
                }
            }
}

        function loadQuestions(){
            let numSections = getCookie('section'); // Use qpoolID as numSections when not 0
            console.log(numSections);
            if (numSections && numSections > 0) {
                const container = document.getElementById("sections-container");
                container.innerHTML = ""; // Clear previous sections
                for (let i = 1; i <= numSections; i++) {
                    let sectionDiv = document.createElement("div");
                    sectionDiv.className = "card shadow-sm p-3 mt-3";
                    sectionDiv.innerHTML = `
                        <div class="card-body">
                            <h5 class="card-title">Section ${i}</h5>
                            <div id='section-${i}' class='question-list border rounded p-2 bg-light'></div>
                        </div>`;
                    container.appendChild(sectionDiv);
                    
                    fetch(`fetch_questions.php?section=${i}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`Network response was not ok: ${response.statusText}`);
                            }
                            return response.text();
                        })
                        .then(data => {
                            document.getElementById(`section-${i}`).innerHTML = data;
                        })
                    .catch(error => console.error("Error fetching questions:", error));
                }
            }
        }

        // Modified event listener with proper async handling
        document.addEventListener("DOMContentLoaded", async function() {
            try {
                let qpool_id = getCookie('qpool_id');
                const response = await fetch(`fetch_questions.php?qpool_id=${encodeURIComponent(qpool_id)}`);
                if (!response.ok) {
                    throw new Error(`Fetch failed with status ${response.status}: ${response.statusText}`);
                }
                const data = await response.text();
                
                if (data.trim() === "failure") {
                    console.log(qpool_id);
                    await loadContent();
                } else {
                    loadQuestions();
                }
            } catch (error) {
                console.error("Error in DOMContentLoaded:", error);
            }
        });

        window.addEventListener("message", function(event) {
            if (event.data === "closeIframe") {
                //document.getElementById("iframe-container").remove();
                document.getElementById("iframe-container").style.display = "none";
                document.getElementById('main-content').classList.remove('blur');
                document.body.classList.remove('modal-open');
                //window.location.reload();
                //loadContent();
                loadQuestions();
                //setTimeout(loadQuestions, 500);
            }
        });

        function openQuestionForm() {
            let content = document.getElementById('main-content');
            content.classList.add('blur');
            document.getElementById("question-iframe").src = "trail.php";
            document.getElementById("iframe-container").style.display = "block";
            document.body.classList.add('modal-open');
        }

        function viewContent(qID) {
            console.log(`View button clicked for content: ${qID}`);
            let content = document.getElementById('main-content');
            content.classList.add('blur');
            document.getElementById("question-iframe").src = `trail.php?qID=${qID}&mode=view`;
            document.getElementById("iframe-container").style.display = "block";
            document.body.classList.add('modal-open');
        }
    </script>
</body>
</html>