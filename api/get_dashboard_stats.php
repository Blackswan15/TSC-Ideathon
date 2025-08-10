<?php
// api/get_dashboard_stats.php
include 'db_connection.php';
header('Content-Type: application/json');

$stats = [
    'active_students' => 0,
    'discussions_today' => 0,
    'ideas_shared' => 0,
    'questions_answered' => 0
];

// Get total number of users (Active Students)
$result = $conn->query("SELECT COUNT(*) as total_users FROM users");
if ($result) {
    $stats['active_students'] = $result->fetch_assoc()['total_users'];
}

// Get discussions posted today
$result = $conn->query("SELECT COUNT(*) as total_discussions FROM posts WHERE post_type = 'discussion' AND DATE(created_at) = CURDATE()");
if ($result) {
    $stats['discussions_today'] = $result->fetch_assoc()['total_discussions'];
}

// Get total ideas shared
$result = $conn->query("SELECT COUNT(*) as total_ideas FROM posts WHERE post_type = 'idea'");
if ($result) {
    $stats['ideas_shared'] = $result->fetch_assoc()['total_ideas'];
}

// Get total questions answered (we'll simulate this for now)
$result = $conn->query("SELECT COUNT(*) as total_questions FROM posts WHERE post_type = 'question'");
if ($result) {
    // This is a placeholder. A real system would track answers separately.
    $stats['questions_answered'] = $result->fetch_assoc()['total_questions'];
}

echo json_encode(['success' => true, 'stats' => $stats]);

$conn->close();
?>