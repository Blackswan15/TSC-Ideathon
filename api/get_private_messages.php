<?php
// api/get_private_messages.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$partner_id = $_GET['partner_id'] ?? 0;
$current_username = $_SESSION['username'];

if (empty($partner_id)) {
    echo json_encode(['success' => false, 'message' => 'Partner ID not provided.']);
    exit();
}

// Check if either user has blocked the other
$block_check_stmt = $conn->prepare("SELECT 1 FROM blocked_users WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?)");
$block_check_stmt->bind_param("iiii", $current_user_id, $partner_id, $partner_id, $current_user_id);
$block_check_stmt->execute();
$block_result = $block_check_stmt->get_result();
$is_blocked = $block_result->num_rows > 0;
$block_check_stmt->close();

$messages = [];
if (!$is_blocked) {
    $sql = "SELECT sender_username, message, created_at 
            FROM private_messages 
            WHERE (sender_username = ? AND receiver_username = (SELECT username FROM users WHERE id=?)) 
            OR (sender_username = (SELECT username FROM users WHERE id=?) AND receiver_username = ?)
            ORDER BY created_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siss", $current_username, $partner_id, $partner_id, $current_username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
}

echo json_encode(['success' => true, 'messages' => $messages, 'current_username' => $current_username, 'is_blocked' => $is_blocked]);
$conn->close();
?>