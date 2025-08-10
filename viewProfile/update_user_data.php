<?php
header('Content-Type: application/json');

$connect = new mysqli("localhost", "root", "", "qgen1");
if ($connect->connect_error) {
  die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

//$_COOKIE['user_id']=7;

if (!isset($_COOKIE['user_id'])) {
  die(json_encode(['success' => false, 'message' => 'User not logged in']));
}

if (!isset($_POST['field']) || !isset($_POST['value'])) {
  die(json_encode(['success' => false, 'message' => 'Missing required parameters']));
}

$allowed_fields = ['fname', 'lname', 'email'];
$field = $_POST['field'];
$value = $_POST['value'];

if (!in_array($field, $allowed_fields)) {
  die(json_encode(['success' => false, 'message' => 'Invalid field']));
}

if ($field === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
  die(json_encode(['success' => false, 'message' => 'Invalid email format']));
}

$user_id = (int)$_COOKIE['user_id'];
$stmt = $connect->prepare("UPDATE account SET $field = ? WHERE user_id = ?");
$stmt->bind_param("si", $value, $user_id);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Field updated successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update field']);
}

$stmt->close();
$connect->close();
?>