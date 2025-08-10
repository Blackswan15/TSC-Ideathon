<?php
// api/vote_post.php
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$post_id = $data['post_id'];
$vote_type = $data['vote_type']; // 'up' or 'down'
$voter_id = $_SESSION['user_id'];

if (empty($post_id) || !in_array($vote_type, ['up', 'down'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
    exit();
}

// 1. Record the vote to prevent duplicates
$stmt = $conn->prepare("INSERT IGNORE INTO post_votes (post_id, user_id, vote_type) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $post_id, $voter_id, $vote_type);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'You have already voted on this post.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();

// 2. Update the vote count on the posts table
$column_to_update = ($vote_type === 'up') ? 'upvotes' : 'downvotes';
$conn->query("UPDATE posts SET $column_to_update = $column_to_update + 1 WHERE id = $post_id");

// 3. Check if points should be awarded/deducted
$result = $conn->query("SELECT user_id, upvotes, downvotes, points_awarded FROM posts WHERE id = $post_id");
$post = $result->fetch_assoc();
$author_id = $post['user_id'];
$points_awarded = $post['points_awarded'];
$net_votes = $post['upvotes'] - $post['downvotes'];

if (!$points_awarded) {
    $points_to_change = 0;
    if ($net_votes >= 10) { // Threshold for positive points
        $points_to_change = 30;
    } elseif ($net_votes <= -5) { // Threshold for negative points
        $points_to_change = -30;
    }

    if ($points_to_change !== 0) {
        $conn->query("UPDATE users SET credential_points = credential_points + $points_to_change WHERE id = $author_id");
        $conn->query("UPDATE posts SET points_awarded = TRUE WHERE id = $post_id");
    }
}

echo json_encode(['success' => true]);
$conn->close();
?>