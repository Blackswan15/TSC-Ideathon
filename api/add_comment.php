<?php
// api/add_comment.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];
$comment_content = $data['comment_content'];
$user_id = $_SESSION['user_id'];

if (empty($post_id) || empty($comment_content)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $user_id, $comment_content);

if ($stmt->execute()) {
    // Award a small number of points for engagement
    $conn->query("UPDATE users SET credential_points = credential_points + 2 WHERE id = $user_id");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add comment.']);
}

$stmt->close();
$conn->close();
?>