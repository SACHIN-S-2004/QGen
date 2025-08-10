<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "qgen1");
// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]));
}

// Create table for pattern metadata
$query = "CREATE TABLE IF NOT EXISTS chapter_patterns (
    p_id INT AUTO_INCREMENT PRIMARY KEY,
    p_name VARCHAR(255),
    sec_num INT NOT NULL,
    chap_num INT NOT NULL
)";
if (!$conn->query($query)) {
    die(json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]));
}

// Create table for chapter details with fixed columns for 8 chapters and OR question
$query = "CREATE TABLE IF NOT EXISTS chapter_details (
    p_id INT,
    section VARCHAR(10),
    chap1 TINYINT(1) DEFAULT 0,
    chap2 TINYINT(1) DEFAULT 0,
    chap3 TINYINT(1) DEFAULT 0,
    chap4 TINYINT(1) DEFAULT 0,
    chap5 TINYINT(1) DEFAULT 0,
    chap6 TINYINT(1) DEFAULT 0,
    chap7 TINYINT(1) DEFAULT 0,
    chap8 TINYINT(1) DEFAULT 0,
    or_allowed TINYINT(1) DEFAULT 0,
    FOREIGN KEY (p_id) REFERENCES chapter_patterns(p_id)
)";
if (!$conn->query($query)) {
    die(json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]));
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$sections = $data['sections'];
$chapters = $data['chapters'];
$patternName = $data['pattern_name'];
$patterns = $data['patterns'];

// Insert into chapter_patterns table
$stmt = $conn->prepare("INSERT INTO chapter_patterns (p_name, sec_num, chap_num) VALUES (?, ?, ?)");
$stmt->bind_param("sii", $patternName, $sections, $chapters);
$stmt->execute();
$pattern_id = $conn->insert_id;
$stmt->close();

$sectionLabels = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J"); // Up to 10 sections
$i = 0;

// Insert into chapter_details table
$stmt = $conn->prepare("INSERT INTO chapter_details (p_id, section, chap1, chap2, chap3, chap4, chap5, chap6, chap7, chap8, or_allowed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($patterns as $pattern) {
    $currentSection = $sectionLabels[$i % count($sectionLabels)];
    $chapterStates = $pattern['chapters']; // Array of booleans
    $orAllowed = $pattern['or_allowed'] ? 1 : 0;

    // Pad or truncate chapter states to exactly 8 columns
    $chapValues = array_fill(0, 8, 0); // Default all to 0
    for ($j = 0; $j < min($chapters, 8); $j++) {
        $chapValues[$j] = $chapterStates[$j] ? 1 : 0; // Convert true/false to 1/0
    }

    $stmt->bind_param(
        "issiiiiiiii", // 1 string (section), 10 integers (p_id, chap1-8, or_allowed)
        $pattern_id,
        $currentSection,
        $chapValues[0],
        $chapValues[1],
        $chapValues[2],
        $chapValues[3],
        $chapValues[4],
        $chapValues[5],
        $chapValues[6],
        $chapValues[7],
        $orAllowed
    );
    $stmt->execute();
    
    $i++;
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);
?>