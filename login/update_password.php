<?php
session_start();
header('Content-Type: application/json');

ob_start(); // Start output buffering

$connect = new mysqli("localhost", "root", "", "qgen1");
if ($connect->connect_error) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $connect->prepare("UPDATE account SET password = ? WHERE email = ?");
if (!$stmt) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Database query preparation failed']);
    $connect->close();
    exit;
}
$stmt->bind_param("ss", $hashed_password, $email);
$success = $stmt->execute();

if ($success) {
    unset($_SESSION['email']);
    ob_end_clean();
    echo json_encode(['success' => true]);
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}

$stmt->close();
$connect->close();
?>