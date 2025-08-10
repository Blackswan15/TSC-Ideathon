<?php
// api/send_chat_message.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'];
$user_id = $_SESSION['user_id'];

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO group_chat (user_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
}

$stmt->close();
$conn->close();
?>