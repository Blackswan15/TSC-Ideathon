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
// Assuming discussions are posts that are NOT complaints (is_complaint = 0)
$result = $conn->query("SELECT COUNT(*) as total_discussions FROM posts WHERE is_complaint = 0 AND DATE(created_at) = CURDATE()");
if ($result) {
    $stats['discussions_today'] = $result->fetch_assoc()['total_discussions'];
}

// Get total ideas shared
// Assuming "ideas" are also non-complaint posts (could refine further if needed)
$result = $conn->query("SELECT COUNT(*) as total_ideas FROM posts WHERE is_complaint = 0");
if ($result) {
    $stats['ideas_shared'] = $result->fetch_assoc()['total_ideas'];
}

// Get total complaints as a stand-in for "questions answered"
// Since no 'answers' table exists, we'll simulate with complaints
$result = $conn->query("SELECT COUNT(*) as total_complaints FROM posts WHERE is_complaint = 1");
if ($result) {
    $stats['questions_answered'] = $result->fetch_assoc()['total_complaints'];
}

echo json_encode(['success' => true, 'stats' => $stats]);

$conn->close();
?>
