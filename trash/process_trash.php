
<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "qgen1");

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

// Check if user is logged in
if (!isset($_COOKIE['user_id']) || empty($_COOKIE['user_id'])) {
    die(json_encode(['success' => false, 'error' => 'Please log in to perform this action']));
}

$user_id = intval($_COOKIE['user_id']);
$action = $_POST['action'];
$qpool_id = intval($_POST['qpool_id']);

// Verify ownership before proceeding
/*$check_sql = "SELECT user_id FROM qpool WHERE qpool_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $qpool_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    $check_stmt->close();
    $conn->close();
    die(json_encode(['success' => false, 'error' => 'Question pool not found']));
}

$row = $result->fetch_assoc();
if ($row['user_id'] !== $user_id) {
    $check_stmt->close();
    $conn->close();
    die(json_encode(['success' => false, 'error' => 'You do not have permission to modify this question pool']));
}
$check_stmt->close();*/

if ($action === 'restore') {
    $sql = "UPDATE qpool SET `show` = 1 WHERE qpool_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $qpool_id, $user_id);
} elseif ($action === 'delete') {
    $sql = "DELETE FROM qpool WHERE qpool_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $qpool_id, $user_id);
} else {
    $conn->close();
    die(json_encode(['success' => false, 'error' => 'Invalid action']));
}

$success = $stmt->execute();
echo json_encode(['success' => $success]);
$stmt->close();
$conn->close();
?>