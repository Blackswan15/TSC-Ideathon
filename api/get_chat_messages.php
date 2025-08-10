<?php
// api/get_chat_messages.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

$current_user_id = $_SESSION['user_id'] ?? 0;

$messages = [];
$sql = "SELECT gc.user_id, gc.message, gc.created_at, u.username 
        FROM group_chat gc
        JOIN users u ON gc.user_id = u.id 
        ORDER BY gc.created_at ASC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

echo json_encode(['success' => true, 'messages' => $messages, 'current_user_id' => $current_user_id]);
$conn->close();
?>