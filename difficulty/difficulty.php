<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Paper Pattern Generator</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .disabled-table {
            opacity: 0.5;
            pointer-events: none;
        }
        .table-container {
            margin-top: 20px;
        }
        .input-cell {
            width: 70px;
            text-align: center;
        }
        .table{
            width:60%;
        }
        .close-button-container {
            position: absolute;
            top: 60px; /* Adjust this value to control vertical position */
            right: 550px; /* Adjust this value to control how far from the right edge */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="close-button-container">
            <button type="button" class="btn-close" aria-label="Close" onclick="window.location.href='../homepage.php'"></button>
        </div>
        <h2 class="mb-4">Set Difficulty Pattern</h2>
        
        <!-- Section Input Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="number" id="sectionCount" class="form-control" placeholder="Enter number of sections" min="1">
                    <button class="btn btn-primary ms-2" onclick="generateTable()">Generate</button>
                </div>
                <div class="mb-3">
                    <input type="text" id="patternName" class="form-control" placeholder="Enter pattern name">
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container">
            <table class="table table-bordered disabled-table" id="patternTable">
                <thead class="table-active">
                    <tr>
                        <th scope="col">Section</th>
                        <th scope="col">Easy</th>
                        <th scope="col">Medium</th>
                        <th scope="col">Hard</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Default 3 sections -->
                    <tr>
                        <td>PART A</td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                    </tr>
                    <tr>
                        <td>PART B</td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                    </tr>
                    <tr>
                        <td>PART C</td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                        <td><input type="number" class="form-control input-cell" min="0"></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-success mt-3" onclick="submitToDatabase()" disabled id="submitDbBtn">Submit</button>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSectionCount = 0;

        function generateTable() {
            const sectionCount = parseInt(document.getElementById('sectionCount').value);
            const patternName = document.getElementById('patternName').value.trim();
            const tableBody = document.getElementById('tableBody');
            const table = document.getElementById('patternTable');
            const submitDbBtn = document.getElementById('submitDbBtn');

            // Validate input
            if (!sectionCount || sectionCount < 1) {
                alert('Please enter a valid number of sections (minimum 1)');
                return;
            }
            if (sectionCount > 5) {
                alert('Only 5 sections are allowed');
                return;
            }

            // Clear existing rows
            tableBody.innerHTML = '';

            // Generate new rows based on section count
            for (let i = 0; i < sectionCount; i++) {
                const sectionName = `PART ${String.fromCharCode(65 + i)}`;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${sectionName}</td>
                    <td><input type="number" class="form-control input-cell" min="0"></td>
                    <td><input type="number" class="form-control input-cell" min="0"></td>
                    <td><input type="number" class="form-control input-cell" min="0"></td>
                    <td><input type="number" class="form-control input-cell" min="0"></td>
                `;
                tableBody.appendChild(row);
            }

            // Enable table and submit button
            table.classList.remove('disabled-table');
            submitDbBtn.disabled = false;
            currentSectionCount = sectionCount;
        }

        function validateTotals() {
            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');
            let isValid = true;
            let warningMessage = '';

            for (let i = 0; i < rows.length; i++) {
                const inputs = rows[i].getElementsByTagName('input');
                const section = rows[i].getElementsByTagName('td')[0].textContent;
                const easy = parseInt(inputs[0].value) || 0;
                const medium = parseInt(inputs[1].value) || 0;
                const hard = parseInt(inputs[2].value) || 0;
                const total = parseInt(inputs[3].value) || 0;
                const sum = easy + medium + hard;

                if (sum !== total) {
                    isValid = false;
                    warningMessage += `<div>${section}: Easy(${easy}) + Medium(${medium}) + Hard(${hard}) = ${sum}, but Total is ${total}</div>`;
                }
            }

            return { isValid, warningMessage };
        }

        function showWarning(message) {
            const tableContainer = document.querySelector('.table-container');
            let warningDiv = document.getElementById('warningAlert');
            
            if (!warningDiv) {
                warningDiv = document.createElement('div');
                warningDiv.id = 'warningAlert';
                tableContainer.insertBefore(warningDiv, tableContainer.querySelector('table'));
            }

            warningDiv.innerHTML = `
                <div class=" alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Warning!</strong> The following sections don't add up correctly:
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
        }

        function submitToDatabase() {
            const patternName = document.getElementById('patternName').value.trim();
            if (!patternName) {
                alert('Please enter a pattern name');
                return;
            }

            const { isValid, warningMessage } = validateTotals();

            if (!isValid) {
                showWarning(warningMessage);
                return;
            }

            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');
            const data = { 
                sections: currentSectionCount, 
                pattern_name: patternName, 
                patterns: [] 
            };

            for (let row of rows) {
                const inputs = row.getElementsByTagName('input');
                const pattern = {
                    easy: parseInt(inputs[0].value) || 0,
                    medium: parseInt(inputs[1].value) || 0,
                    hard: parseInt(inputs[2].value) || 0,
                    total: parseInt(inputs[3].value) || 0
                };
                data.patterns.push(pattern);
            }

            fetch('save_difficulty.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Pattern saved successfully!');
                } else {
                    alert('Error saving pattern: ' + result.error);
                }
            })
            //console.log('Error: ' + error);
            .catch(error => alert('Error: ' + error));
        }

        // Clear input fields on page load
        window.onload = function() {
            document.getElementById('sectionCount').value = '';
            document.getElementById('patternName').value = '';
        }
    </script>
</body>
</html>