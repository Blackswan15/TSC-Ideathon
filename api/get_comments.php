<?php
// api/get_comments.php
include 'db_connection.php';
header('Content-Type: application/json');

$post_id = $_GET['post_id'] ?? 0;

if (empty($post_id)) {
    echo json_encode(['success' => false, 'comments' => []]);
    exit();
}

$comments = [];
$stmt = $conn->prepare("SELECT c.comment_content, c.created_at, u.username 
                        FROM comments c 
                        JOIN users u ON c.user_id = u.id 
                        WHERE c.post_id = ? 
                        ORDER BY c.created_at ASC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

echo json_encode(['success' => true, 'comments' => $comments]);
$stmt->close();
$conn->close();
?>