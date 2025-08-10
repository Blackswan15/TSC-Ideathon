<?php
// api/send_private_message.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$receiver_id = $data['receiver_id'];
$message = $data['message'];
$sender_id = $_SESSION['user_id'];
$sender_username = $_SESSION['username'];

if (empty($receiver_id) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    exit();
}

// Check if the receiver has blocked the sender
$block_check_stmt = $conn->prepare("SELECT 1 FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?");
$block_check_stmt->bind_param("ii", $receiver_id, $sender_id);
$block_check_stmt->execute();
if ($block_check_stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have been blocked by this user.']);
    exit();
}
$block_check_stmt->close();

$receiver_username_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$receiver_username_stmt->bind_param("i", $receiver_id);
$receiver_username_stmt->execute();
$receiver_username = $receiver_username_stmt->get_result()->fetch_assoc()['username'];

$stmt = $conn->prepare("INSERT INTO private_messages (sender_username, receiver_username, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $sender_username, $receiver_username, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}

$stmt->close();
$conn->close();
?>