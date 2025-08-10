<?php
header('Content-Type: application/json');

$connect = new mysqli("localhost", "root", "", "qgen1");
if ($connect->connect_error) {
  die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

//$_COOKIE['user_id']=12;
if (isset($_GET['viewProfile'])) {
  
  if (!isset($_COOKIE['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
  }
  
  $user_id = (int)$_COOKIE['user_id'];
  $stmt = $connect->prepare("SELECT profile_pic, fname, lname, username, email FROM account WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($row = $result->fetch_assoc()) {
    $row['profile_pic'] = $row['profile_pic'] ? base64_encode($row['profile_pic']) : null; // Convert BLOB to base64
    echo json_encode(['success' => true, 'data' => $row]);
  } else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
  }
}

if (isset($_GET['section'])) {
  if (!isset($_COOKIE['user_id'])) {
    die(json_encode(['success' => false, 'message' => 'User not logged in']));
  }
  
  $user_id = (int)$_COOKIE['user_id'];
  $stmt = $connect->prepare("SELECT profile_pic FROM account WHERE user_id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($row = $result->fetch_assoc()) {
    $row['profile_pic'] = $row['profile_pic'] ? base64_encode($row['profile_pic']) : null; // Convert BLOB to base64
    echo json_encode(['success' => true, 'data' => $row]);
  } else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
  }
}

$stmt->close();
$connect->close();
?>