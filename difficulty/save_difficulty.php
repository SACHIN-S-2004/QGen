<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost","root","","qgen1");
// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]));
}

$query = "CREATE TABLE IF NOT EXISTS patterns (p_id INT AUTO_INCREMENT PRIMARY KEY,p_name VARCHAR(255),sec_num INT NOT NULL)";
if(!($Result = $conn->query($query))){
    die(json_encode(['success' => false, 'error' => 'Database error: ' . $conn->connect_error]));
    exit;
}

$query = "CREATE TABLE IF NOT EXISTS section_details (p_id INT,easy INT DEFAULT 0,med INT DEFAULT 0,hard INT DEFAULT 0,qNum INT DEFAULT 0,FOREIGN KEY (p_id) REFERENCES patterns(p_id))";
if(!($Result = $conn->query($query))){
    die(json_encode(['success' => false, 'error' => 'Database error: ' . $conn->connect_error]));
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$sections = $data['sections'];
$patternName = $data['pattern_name'];
$patterns = $data['patterns'];

// Insert into patterns table
$stmt = $conn->prepare("INSERT INTO patterns (p_name, sec_num) VALUES (?, ?)");
$stmt->bind_param("si", $patternName, $sections);
$stmt->execute();
$pattern_id = $conn->insert_id;
$stmt->close();

$section = array("A", "B", "C", "D", "E");
$i = 0;

// Insert into section_details table
$stmt = $conn->prepare("INSERT INTO section_details (p_id, easy, med, hard, qNum, section) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($patterns as $pattern) {
    // Prevent $i from exceeding the number of sections
    $currentSection = $section[$i % count($section)];

    // Bind parameters and execute the statement
    $stmt->bind_param("iiiiis", $pattern_id, $pattern['easy'], $pattern['medium'], $pattern['hard'], $pattern['total'], $currentSection);
    $stmt->execute();
    
    $i++;
}

$stmt->close();

$conn->close();

echo json_encode(['success' => true]);
?>