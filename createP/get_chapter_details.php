<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chapter Pattern Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h4>Chapter Pattern Details</h4>
    <?php
    $conn = new mysqli("localhost", "root", "", "qgen1");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $chapter_pattern_id = $_GET['chapter_pattern_id'] ?? null;
    if (!$chapter_pattern_id) {
        echo "<p>No chapter pattern selected.</p>";
        exit;
    }

    $query = "SELECT * FROM chapter_details WHERE p_id = $chapter_pattern_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Section</th><th>Chapter 1</th><th>Chapter 2</th><th>Chapter 3</th><th>Chapter 4</th><th>Chapter 5</th><th>Chapter 6</th><th>Chapter 7</th><th>Chapter 8</th><th>OR Allowed</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['section'] . "</td>";
            echo "<td>" . ($row['chap1'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap2'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap3'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap4'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap5'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap6'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap7'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['chap8'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['or_allowed'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No details available for this chapter pattern.</p>";
    }

    $conn->close();
    ?>
    <button class="btn btn-secondary mt-3" onclick="window.parent.postMessage('closeIframe', '*')">Close</button>
</body>
</html>