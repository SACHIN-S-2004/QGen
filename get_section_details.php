<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "qgen1");
$section = $_GET['section'] ?? '';
$qpool_id = $_GET['qpool_id'] ?? '';

$sections = [];
if ($section) {
    $sections[] = ['section' => $section]; // Simplified for now; adjust if multiple sections needed
}

echo json_encode(['sections' => $sections]);
$conn->close();
?>