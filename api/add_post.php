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
$is_complaint = isset($data['is_complaint']) ? (int)$data['is_complaint'] : 0; // default: not a complaint
$user_id = $_SESSION['user_id'];

if (empty($post_content)) {
    echo json_encode(['success' => false, 'message' => 'Post content cannot be empty.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO posts (user_id, post_content, is_complaint, upvotes, downvotes, points_awarded, created_at) VALUES (?, ?, ?, 0, 0, 0, NOW())");
$stmt->bind_param("isi", $user_id, $post_content, $is_complaint);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add post.']);
}

$stmt->close();
$conn->close();
?>
