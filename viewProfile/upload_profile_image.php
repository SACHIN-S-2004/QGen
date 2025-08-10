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

if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
  $error_code = $_FILES['profile_pic']['error'] ?? 'No file';
  die(json_encode(['success' => false, 'message' => "Upload error: $error_code"]));
}

$file = $_FILES['profile_pic'];
$allowed_types = ['image/png', 'image/jpeg'];
if (!in_array($file['type'], $allowed_types)) {
  die(json_encode(['success' => false, 'message' => 'Only PNG or JPG allowed']));
}

if ($file['size'] > 5 * 1024 * 1024) {
  die(json_encode(['success' => false, 'message' => 'Image must be under 5MB']));
}

$image_data = file_get_contents($file['tmp_name']);
$user_id = (int)$_COOKIE['user_id'];
$stmt = $connect->prepare("UPDATE account SET profile_pic = ? WHERE user_id = ?");
$stmt->bind_param("bi", $image_data, $user_id);
$stmt->send_long_data(0, $image_data); // For LONGBLOB

if ($stmt->execute()) {
  $profile_pic_base64 = base64_encode($image_data);
  echo json_encode(['success' => true, 'message' => 'Profile picture updated', 'profile_pic' => $profile_pic_base64]);
} else {
  echo json_encode(['success' => false, 'message' => 'Failed to update profile picture']);
}

$stmt->close();
$connect->close();
?>