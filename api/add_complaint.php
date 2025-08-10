<?php
// api/add_complaint.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$complaint_content = $data['complaint_content'];
$post_id_reported = $data['post_id'] ?? null; // Optional post_id being reported
$user_id = $_SESSION['user_id'];

if (empty($complaint_content)) {
    echo json_encode(['success' => false, 'message' => 'Complaint content cannot be empty.']);
    exit();
}

// The content now includes a reference if it's a report against a specific post
$final_content = $post_id_reported ? "Report against Post #{$post_id_reported}: {$complaint_content}" : $complaint_content;

$stmt = $conn->prepare("INSERT INTO posts (user_id, post_content, is_complaint) VALUES (?, ?, TRUE)");
$stmt->bind_param("is", $user_id, $final_content);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Complaint submitted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit complaint.']);
}

$stmt->close();
$conn->close();
?>