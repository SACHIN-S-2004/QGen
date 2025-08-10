<!-- fetch_trash.php with ownership validation -->
<?php
$conn = new mysqli("localhost", "root", "", "qgen1");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_COOKIE['user_id']) || empty($_COOKIE['user_id'])) {
    echo "<tr><td colspan='3' class='text-center py-4'>Please log in to view your trashed question pools</td></tr>";
    exit;
}

$user_id = intval($_COOKIE['user_id']);
$auto_delete_days = 14;

// Delete expired items for this user
$cleanup_sql = "DELETE FROM qpool WHERE `show` = 0 AND user_id = ? AND deletion_date <= DATE_SUB(NOW(), INTERVAL ? DAY)";
$stmt = $conn->prepare($cleanup_sql);
$stmt->bind_param("ii", $user_id, $auto_delete_days);
$stmt->execute();
$stmt->close();

// Fetch trashed items for this user only
$sql = "SELECT qpool_id, name, time, deletion_date FROM qpool WHERE `show` = 0 AND user_id = ? ORDER BY time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $delete_time = new DateTime($row["deletion_date"]);
        $delete_time->modify("+{$auto_delete_days} days");
        $remaining = $delete_time->diff(new DateTime());
        $days_left = $remaining->days;
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
        echo "<td>" . $row["time"] . "</td>";
        echo "<td>" . $row["deletion_date"] . "<br><span class='remaining-time'>Auto-deletes in: $days_left days</span></td>";
        echo "<td>";
        echo "<button class='btn btn-primary btn-sm btn-action' onclick='restoreQuestionPool(" . $row["qpool_id"] . ")'>Restore</button>";
        echo "<button class='btn btn-danger btn-sm btn-action' onclick='deleteQuestionPool(" . $row["qpool_id"] . ")'>Delete</button>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center py-4'>No trashed question pools found</td></tr>";
}

$stmt->close();
$conn->close();
?>