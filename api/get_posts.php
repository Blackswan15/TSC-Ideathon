<?php
// api/get_posts.php
include 'db_connection.php';
header('Content-Type: application/json');

$posts = [];
$sql = "SELECT 
            p.id as post_id, 
            p.post_content as content, 
            p.is_complaint,
            p.created_at, 
            p.upvotes, 
            p.downvotes, 
            u.username,
            (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
        FROM posts p 
        JOIN users u ON p.user_id = u.id 
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

echo json_encode(['success' => true, 'posts' => $posts]);
$conn->close();
?>