<?php
// api/get_all_users.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}
$current_user_id = $_SESSION['user_id'];

$users = [];
// This query now excludes users who have blocked the current user
$sql = "SELECT id, username FROM users u
        WHERE u.id != ? 
        AND NOT EXISTS (
            SELECT 1 FROM blocked_users b WHERE b.blocker_id = u.id AND b.blocked_id = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'users' => $users]);
$stmt->close();
$conn->close();
?>