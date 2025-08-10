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

if (!isset($_POST['current_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
  die(json_encode(['success' => false, 'message' => 'Missing required parameters']));
}

$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
  die(json_encode(['success' => false, 'message' => 'New passwords do not match']));
}

if (strlen($new_password) < 8) {
  die(json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']));
}

$user_id = (int)$_COOKIE['user_id'];
$stmt = $connect->prepare("SELECT password FROM account WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!password_verify($current_password, $user['password'])) {
  die(json_encode(['success' => false, 'message' => 'Current password is incorrect']));
}

$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $connect->prepare("UPDATE account SET password = ? WHERE user_id = ?");
$stmt->bind_param("si", $new_password_hash, $user_id);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update password']);
}

$stmt->close();
$connect->close();
?>