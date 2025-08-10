<?php
    require 'vendor/autoload.php'; // Include PhpOffice\PhpWord via Composer
    use PhpOffice\PhpWord\IOFactory;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Questions - Question Paper Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .instructions {
            margin-bottom: 20px;
            color: #666;
        }
        .instructions h3 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        #submitBtn {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #submitBtn:hover:not(:disabled) {
            background-color: #0056b3;
        }
        #submitBtn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #status {
            margin-top: 20px;
            font-style: italic;
            color: #333;
        }
        .report {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #fafafa;
        }
        .report h3 {
            margin-top: 0;
            color: #333;
        }
        .report ul {
            list-style: none;
            padding: 0;
        }
        .report li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .report li:last-child {
            border-bottom: none;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-between">
        <h1 class="mx-auto">Import Questions</h1>
        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='../Qc/questionC.php'"></button>
    </div>
    <div class="container">
        <div class="instructions">
            <h3>Instructions for Formatting Your .docx File</h3>
            <p>To import questions successfully, please follow this format in your .docx file:</p>
            <ul>
                <li>Each question must be on a <strong>new paragraph</strong>.</li>
                <li>Include the question text followed by metadata in square brackets <code>[...]</code>.</li>
                <li>Metadata format: <code>[QType, Difficulty, Section, Chapter, Mark]</code> using shorthand:
                    <ul>
                        <li><strong>QType</strong>: <code>p</code> (plain), <code>s</code> (sub-parts), <code>i</code> (img)</li>
                        <li><strong>Difficulty</strong>: <code>e</code> (easy), <code>m</code> (medium), <code>h</code> (hard)</li>
                        <li><strong>Section</strong>: <code>A</code>, <code>B</code>, <code>C</code>, etc. (single uppercase letter)</li>
                        <li><strong>Chapter</strong>: Positive integer (e.g., 1, 2, 5)</li>
                        <li><strong>Mark</strong>: Positive integer (e.g., 1, 8, 5)</li>
                    </ul>
                </li>
                <li>For <code>s</code> (sub-parts): Write the main question, then sub-parts as (a) to (e) (max 5 sub-parts).</li>
                <li>For <code>i</code> (img): Only text is imported; images are not supported yet.</li>
            </ul>
            <p><strong>Example:</strong></p>
            <pre>
What is the capital of France? [p,e,A,2,1]
Solve: (a) 2 + 3 (b) 5 - 1 [s,m,B,5,3]
Describe the water cycle with a diagram. [i,h,C,3,10]
            </pre>
        </div>
        <form id="importForm" action="import_questions.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fileInput">Upload .docx File:</label>
                <input type="file" id="fileInput" name="docxFile" accept=".docx" required>
            </div>
            <button type="submit" id="submitBtn" name="submit">Import Questions</button>
        </form>
        <div id="status">
            <?php if (isset($status)) echo $status; ?>
        </div>
        <?php if (isset($report)) echo $report; ?>
    </div>

    <script>
        const fileInput = document.getElementById('fileInput');
        const submitBtn = document.getElementById('submitBtn');
        const status = document.getElementById('status');

        fileInput.addEventListener('change', function() {
            const file = fileInput.files[0];
            if (file) {
                const fileName = file.name.toLowerCase();
                if (!fileName.endsWith('.docx')) {
                    submitBtn.disabled = true;
                    status.textContent = 'Error: Please upload a .docx file only.';
                } else {
                    submitBtn.disabled = false;
                    status.textContent = 'Ready to import. Click the button to proceed.';
                }
            } else {
                submitBtn.disabled = true;
                status.textContent = 'Error: No file selected.';
            }
        });

        document.getElementById('importForm').addEventListener('submit', function() {
            status.textContent = 'Uploading and processing...';
        });
    </script>
</body>
</html>

<?php
if (isset($_POST['submit'])) {

    $connect = new mysqli("localhost", "root", "", "qgen1");
    if ($connect->connect_error) {
        $status = "Error: Database connection failed - " . $connect->connect_error;
        echo "<script>document.getElementById('status').innerHTML = '$status';</script>";
        exit;
    }

    
    //$qpool_id = 22;
    $qpool_id = isset($_COOKIE['qpool_id']) ? (int)$_COOKIE['qpool_id'] : null;
    if (!$qpool_id) {
        $status = "Error: Question pool ID not found in cookies.";
        echo "<script>document.getElementById('status').innerHTML = '$status';</script>";
        $connect->close();
        exit;
    }

    if (isset($_FILES['docxFile']) && $_FILES['docxFile']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['docxFile']['tmp_name'];
        $fileName = $_FILES['docxFile']['name'];
        
        if (pathinfo($fileName, PATHINFO_EXTENSION) !== 'docx') {
            $status = "Error: Only .docx files are allowed.";
            echo "<script>document.getElementById('status').innerHTML = '$status';</script>";
            $connect->close();
            exit;
        }

        $phpWord = IOFactory::load($fileTmpPath);
        $questionsImported = 0;
        $processedParagraphs = [];
        $reportItems = [];

        foreach ($phpWord->getSections() as $section) {
            $paragraphText = '';
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $paragraphText .= $element->getText();
                }
                if ($element instanceof \PhpOffice\PhpWord\Element\TextBreak || $element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $text = trim($paragraphText);
                    if (!empty($text) && !in_array($text, $processedParagraphs)) {
                        $processedParagraphs[] = $text;

                        // Extract question content and metadata
                        preg_match('/^(.*?)\s*\[(.*?)\]$/', $text, $matches);
                        if (count($matches) < 3) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Missing or invalid metadata format'];
                            $paragraphText = ''; // Reset for next paragraph
                            continue;
                        }

                        $fullContent = trim($matches[1]);
                        $metadata = $matches[2];

                        $metaParts = array_map('trim', explode(',', $metadata));
                        if (count($metaParts) !== 5) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Metadata must have exactly 5 parts'];
                            $paragraphText = '';
                            continue;
                        }

                        [$qtypeShort, $diffShort, $section, $chapter, $mark] = $metaParts;

                        $qtypeMap = ['p' => 'plain', 's' => 'sub', 'i' => 'img'];
                        $difficultyMap = ['e' => 'easy', 'm' => 'medium', 'h' => 'hard'];

                        $qtype = $qtypeMap[strtolower($qtypeShort)] ?? null;
                        $difficulty = $difficultyMap[strtolower($diffShort)] ?? null;
                        $chapter = (int)$chapter;
                        $mark = (int)$mark;

                        $validQTypes = ['plain', 'sub', 'img'];
                        $validDifficulties = ['easy', 'medium', 'hard'];

                        if (!in_array($qtype, $validQTypes)) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid QType '$qtypeShort'"];
                            $paragraphText = '';
                            continue;
                        }
                        if (!in_array($difficulty, $validDifficulties)) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid Difficulty '$diffShort'"];
                            $paragraphText = '';
                            continue;
                        }
                        if (!preg_match('/^[A-Z]$/', $section)) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid Section '$section' (must be a single uppercase letter)"];
                            $paragraphText = '';
                            continue;
                        }
                        if ($chapter <= 0 || $chapter >8) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Chapter '$chapter' must be between 1 AND 8"];
                            $paragraphText = '';
                            continue;
                        }
                        if ($mark <= 0) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Mark '$mark' must be a positive integer"];
                            $paragraphText = '';
                            continue;
                        }
                        //$maxSections=3;
                        // Section validation based on $_COOKIE['section']
                        $maxSections = isset($_COOKIE['section']) ? (int)$_COOKIE['section'] : 0;
                        if ($maxSections < 1 || $maxSections > 5) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid section count in cookie: '$maxSections' (must be 1-5)"];
                            $paragraphText = '';
                            continue;
                        }

                        // Convert section letter to numeric position (A=1, B=2, etc.)
                        $sectionPosition = ord(strtoupper($section)) - ord('A') + 1;

                        if ($sectionPosition > 5) { // Beyond 'E'
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Section '$section' exceeds defined sections (A-" . chr(ord('A') + $maxSections - 1) . ")"];
                            $paragraphText = '';
                            continue;
                        }

                        if ($sectionPosition > $maxSections) { // Within A-E but beyond defined sections
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Section '$section' exceeds defined sections (A-" . chr(ord('A') + $maxSections - 1) . ")"];
                            $paragraphText = '';
                            continue;
                        }

                        $content = $fullContent;
                        if ($qtype === 'sub') {
                            preg_match('/^(.*?)(?=\s*\([a-e]\))/', $fullContent, $mainMatch);
                            $content = trim($mainMatch[1] ?? $fullContent);
                            if (empty($content)) {
                                $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'No main question content before sub-parts'];
                                $paragraphText = '';
                                continue;
                            }
                        }

                        $stmt = $connect->prepare("INSERT INTO question (qpool_id, content, QType, difficulty, section, chapter, mark) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Database prepare failed: ' . $connect->error];
                            $paragraphText = '';
                            continue;
                        }
                        $stmt->bind_param("issssii", $qpool_id, $content, $qtype, $difficulty, $section, $chapter, $mark);
                        if ($stmt->execute()) {
                            $questionsImported++;
                            $mainQuestionId = $connect->insert_id;

                            if ($qtype === 'sub') {
                                preg_match_all('/\(([a-e])\)\s*([^()]+)(?=\s*(?:\([a-e]\)|$))/', $fullContent, $subMatches);
                                $subParts = $subMatches[2] ?? [];
                                $subCount = 0;
                                foreach ($subParts as $subContent) {
                                    if ($subCount >= 5) break;
                                    $subContent = trim($subContent);
                                    if (!empty($subContent)) {
                                        $subStmt = $connect->prepare("INSERT INTO SQuestion (qpool_id, Q_id, content) VALUES (?, ?, ?)");
                                        if ($subStmt) {
                                            $subStmt->bind_param("iis", $qpool_id, $mainQuestionId, $subContent);
                                            $subStmt->execute();
                                            $subStmt->close();
                                        }
                                        $subCount++;
                                    }
                                }
                            }

                            if ($qtype === 'img') {
                                // Placeholder for image handling
                            }

                            $reportItems[] = ['text' => $text, 'status' => 'success', 'reason' => 'Imported successfully'];
                        } else {
                            $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Database insertion failed: ' . $stmt->error];
                        }
                        $stmt->close();
                    }
                    $paragraphText = '';
                }
            }
            // Process remaining text
            $text = trim($paragraphText);
            if (!empty($text) && !in_array($text, $processedParagraphs)) {
                $processedParagraphs[] = $text;

                preg_match('/^(.*?)\s*\[(.*?)\]$/', $text, $matches);
                if (count($matches) < 3) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Missing or invalid metadata format'];
                    continue;
                }

                $fullContent = trim($matches[1]);
                $metadata = $matches[2];

                $metaParts = array_map('trim', explode(',', $metadata));
                if (count($metaParts) !== 5) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Metadata must have exactly 5 parts'];
                    continue;
                }

                [$qtypeShort, $diffShort, $section, $chapter, $mark] = $metaParts;

                $qtypeMap = ['p' => 'plain', 's' => 'sub', 'i' => 'img'];
                $difficultyMap = ['e' => 'easy', 'm' => 'medium', 'h' => 'hard'];

                $qtype = $qtypeMap[strtolower($qtypeShort)] ?? null;
                $difficulty = $difficultyMap[strtolower($diffShort)] ?? null;
                $chapter = (int)$chapter;
                $mark = (int)$mark;

                $validQTypes = ['plain', 'sub', 'img'];
                $validDifficulties = ['easy', 'medium', 'hard'];

                if (!in_array($qtype, $validQTypes)) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid QType '$qtypeShort'"];
                    continue;
                }
                if (!in_array($difficulty, $validDifficulties)) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid Difficulty '$diffShort'"];
                    continue;
                }
                if (!preg_match('/^[A-Z]$/', $section)) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid Section '$section' (must be a single uppercase letter)"];
                    continue;
                }
                if ($chapter <= 0 || $chapter > 8){
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Chapter '$chapter' must be between 1 AND 8"];
                    continue;
                }
                if ($mark <= 0) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Mark '$mark' must be a positive integer"];
                    continue;
                }
                //$maxSections=3;
                // Section validation based on $_COOKIE['section']
                $maxSections = isset($_COOKIE['section']) ? (int)$_COOKIE['section'] : 0;
                if ($maxSections < 1 || $maxSections > 5) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Invalid section count in cookie: '$maxSections' (must be 1-5)"];
                    $paragraphText = '';
                    continue;
                }

                // Convert section letter to numeric position (A=1, B=2, etc.)
                $sectionPosition = ord(strtoupper($section)) - ord('A') + 1;

                if ($sectionPosition > 5) { // Beyond 'E'
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Section '$section' exceeds defined sections (A-" . chr(ord('A') + $maxSections - 1) . ")"];
                    $paragraphText = '';
                    continue;
                }

                if ($sectionPosition > $maxSections) { // Within A-E but beyond defined sections
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => "Section '$section' exceeds defined sections (A-" . chr(ord('A') + $maxSections - 1) . ")"];
                    $paragraphText = '';
                    continue;
                }


                $content = $fullContent;
                if ($qtype === 'sub') {
                    preg_match('/^(.*?)(?=\s*\([a-e]\))/', $fullContent, $mainMatch);
                    $content = trim($mainMatch[1] ?? $fullContent);
                    if (empty($content)) {
                        $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'No main question content before sub-parts'];
                        continue;
                    }
                }

                $stmt = $connect->prepare("INSERT INTO question (qpool_id, content, QType, difficulty, section, chapter, mark) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Database prepare failed: ' . $connect->error];
                    continue;
                }
                $stmt->bind_param("issssii", $qpool_id, $content, $qtype, $difficulty, $section, $chapter, $mark);
                if ($stmt->execute()) {
                    $questionsImported++;
                    $mainQuestionId = $connect->insert_id;

                    if ($qtype === 'sub') {
                        preg_match_all('/\(([a-e])\)\s*([^()]+)(?=\s*(?:\([a-e]\)|$))/', $fullContent, $subMatches);
                        $subParts = $subMatches[2] ?? [];
                        $subCount = 0;
                        foreach ($subParts as $subContent) {
                            if ($subCount >= 5) break;
                            $subContent = trim($subContent);
                            if (!empty($subContent)) {
                                $subStmt = $connect->prepare("INSERT INTO SQuestion (qpool_id, Q_id, content) VALUES (?, ?, ?)");
                                if ($subStmt) {
                                    $subStmt->bind_param("iis", $qpool_id, $mainQuestionId, $subContent);
                                    $subStmt->execute();
                                    $subStmt->close();
                                }
                                $subCount++;
                            }
                        }
                    }

                    if ($qtype === 'img') {
                        // Placeholder for image handling
                    }

                    $reportItems[] = ['text' => $text, 'status' => 'success', 'reason' => 'Imported successfully'];
                } else {
                    $reportItems[] = ['text' => $text, 'status' => 'error', 'reason' => 'Database insertion failed: ' . $stmt->error];
                }
                $stmt->close();
            }
        }

        // Generate report
        $status = $questionsImported > 0 
            ? "Successfully imported $questionsImported questions!" 
            : "No valid questions found in the file.";
        
        $report = '<div class="report"><h3>Import Report</h3><ul>';
        foreach ($reportItems as $item) {
            if ($item['status'] === 'success') {
                $report .= "<li class=\"success\">✓ " . htmlspecialchars($item['text']) . " - " . htmlspecialchars($item['reason']) . "</li>";
            } else {
                $report .= "<li class=\"error\">✗ " . htmlspecialchars($item['text']) . " - " . htmlspecialchars($item['reason']) . "</li>";
            }
        }
        $report .= '</ul></div>';

        $connect->close();

        // Output status and report directly
        echo "<script>document.getElementById('status').innerHTML = '" . addslashes($status) . "';</script>";
        echo $report; // Directly echo the report to ensure it displays
    } else {
        $status = "Error uploading file. Please try again.";
        echo "<script>document.getElementById('status').innerHTML = '$status';</script>";
    }
}
?>