<?php
// api/unblock_user.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$blocker_id = $_SESSION['user_id'];
$blocked_id = $data['blocked_id'];

if (empty($blocked_id)) {
    echo json_encode(['success' => false, 'message' => 'User to unblock not specified.']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM blocked_users WHERE blocker_id = ? AND blocked_id = ?");
$stmt->bind_param("ii", $blocker_id, $blocked_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to unblock user.']);
}

$stmt->close();
$conn->close();
?>