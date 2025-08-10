<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Paper Pattern Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .disabled-table {
            opacity: 0.5;
            pointer-events: none;
        }
        .table-container {
            margin-top: 20px;
        }
        .checkbox-cell {
            text-align: center;
        }
        .table {
            width: 60%;
        }
        .close-button-container {
            position: absolute;
            top: 60px; /* Adjust this value to control vertical position */
            right: 600px; /* Adjust this value to control how far from the right edge */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="close-button-container">
            <button type="button" class="btn-close" aria-label="Close" onclick="window.location.href='../homepage.php'"></button>
        </div>
        <h2 class="mb-4">Set Chapter Pattern</h2>
        
        <!-- Input Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="input-group mb-3">
                    <input type="number" id="sectionCount" class="form-control" placeholder="Enter number of sections" min="1">
                    <input type="number" id="chapterCount" class="form-control ms-2" placeholder="Enter number of chapters" min="1">
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
                    <tr id="tableHeader">
                        <th scope="col">Section</th>
                        <!-- Dynamic chapter headers will be added here -->
                        <th scope="col">OR Question Allowed</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Dynamic rows will be generated here -->
                </tbody>
            </table>
            <button class="btn btn-success mt-3" onclick="submitToDatabase()" disabled id="submitDbBtn">Submit</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSectionCount = 0;
        let currentChapterCount = 0;

        function generateTable() {
            const sectionCount = parseInt(document.getElementById('sectionCount').value);
            const chapterCount = parseInt(document.getElementById('chapterCount').value);
            const patternName = document.getElementById('patternName').value.trim();
            const tableHeader = document.getElementById('tableHeader');
            const tableBody = document.getElementById('tableBody');
            const table = document.getElementById('patternTable');
            const submitDbBtn = document.getElementById('submitDbBtn');

            // Validate input
            if (!sectionCount || sectionCount < 1 || !chapterCount || chapterCount < 1) {
                alert('Please enter valid numbers for sections and chapters (minimum 1)');
                return;
            }
            if (sectionCount > 5 || chapterCount > 8) {
                alert('Maximum 5 sections and 8 chapters allowed');
                return;
            }

            // Clear existing content
            tableHeader.innerHTML = '<th scope="col">Section</th>';
            tableBody.innerHTML = '';

            // Generate chapter headers
            for (let i = 1; i <= chapterCount; i++) {
                const th = document.createElement('th');
                th.scope = 'col';
                th.textContent = `Chapter ${i}`;
                tableHeader.appendChild(th);
            }
            // Add OR Question Allowed column header
            const orHeader = document.createElement('th');
            orHeader.scope = 'col';
            orHeader.textContent = 'OR Question Allowed';
            tableHeader.appendChild(orHeader);

            // Generate rows
            for (let i = 0; i < sectionCount; i++) {
                const sectionName = `PART ${String.fromCharCode(65 + i)}`;
                const row = document.createElement('tr');
                let rowHTML = `<td>${sectionName}</td>`;
                
                // Add chapter checkboxes
                for (let j = 0; j < chapterCount; j++) {
                    rowHTML += `
                        <td class="checkbox-cell">
                            <input type="checkbox" class="form-check-input" checked>
                        </td>`;
                }
                // Add OR question checkbox
                rowHTML += `
                    <td class="checkbox-cell">
                        <input type="checkbox" class="form-check-input" checked>
                    </td>`;
                
                row.innerHTML = rowHTML;
                tableBody.appendChild(row);
            }

            // Enable table and submit button
            table.classList.remove('disabled-table');
            submitDbBtn.disabled = false;
            currentSectionCount = sectionCount;
            currentChapterCount = chapterCount;
        }

        function submitToDatabase() {
            const patternName = document.getElementById('patternName').value.trim();
            if (!patternName) {
                alert('Please enter a pattern name');
                return;
            }

            const tableBody = document.getElementById('tableBody');
            const rows = tableBody.getElementsByTagName('tr');
            const data = { 
                sections: currentSectionCount,
                chapters: currentChapterCount,
                pattern_name: patternName,
                patterns: []
            };

            for (let row of rows) {
                const section = row.cells[0].textContent;
                const checkboxes = row.getElementsByTagName('input');
                const pattern = {
                    chapters: [],
                    or_allowed: checkboxes[checkboxes.length - 1].checked
                };
                
                for (let i = 0; i < currentChapterCount; i++) {
                    pattern.chapters.push(checkboxes[i].checked);
                }
                data.patterns.push(pattern);
            }

            fetch('save_pattern.php', {
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
            .catch(error => alert('Error: ' + error));
        }

        // Clear input fields on page load
        window.onload = function() {
            document.getElementById('sectionCount').value = '';
            document.getElementById('chapterCount').value = '';
            document.getElementById('patternName').value = '';
        }
    </script>
</body>
</html>