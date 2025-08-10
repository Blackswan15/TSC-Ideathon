<?php
// api/add_post.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_content = $data['post_content'];
$post_type = $data['post_type'] ?? 'discussion';
$user_id = $_SESSION['user_id'];

if (empty($post_content)) {
    echo json_encode(['success' => false, 'message' => 'Post content cannot be empty.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO posts (user_id, post_content, post_type) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $post_content, $post_type);

if ($stmt->execute()) {
    // NOTE: Points are no longer awarded on post creation.
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add post.']);
}

$stmt->close();
$conn->close();
?>