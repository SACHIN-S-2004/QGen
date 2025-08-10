<?php
$connect = new mysqli("localhost", "root", "", "qgen1");
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$qpool_id = $_GET['qpool'];
$complete = $_GET['complete'];

$stmt = $connect->prepare("UPDATE qpool SET completed = ? WHERE qpool_id = ?");
$stmt->bind_param("ii", $complete, $qpool_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error: " . $connect->error;
}

$stmt->close();
$connect->close();
?>