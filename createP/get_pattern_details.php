<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pattern Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 20px;
            background: white;
        }
    </style>
</head>
<body>
    <div class="modal-header">
        <h5 class="modal-title">Pattern Details</h5>
        <!--<button type="button" class="btn-close" onclick="window.parent.postMessage('closeIframe', '*');"></button>-->
    </div>
    <div class="modal-body m-2">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Easy</th>
                    <th>Medium</th>
                    <th>Hard</th>
                    <th>Total Questions</th>
                </tr>
            </thead>
            <tbody id="patternTableBody">
                <?php
                $conn = new mysqli("localhost", "root", "", "qgen1");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $pattern_id = $_GET['pattern_id'];
                $query = "SELECT * FROM section_details WHERE p_id = $pattern_id"; // Adjust table/columns as needed
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>Part {$row['section']}</td>";
                    echo "<td>{$row['easy']}</td>";
                    echo "<td>{$row['med']}</td>";
                    echo "<td>{$row['hard']}</td>";
                    echo "<td>{$row['qNum']}</td>";
                    echo "</tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="window.parent.postMessage('closeIframe', '*');">Close</button>
    </div>
</body>
</html>