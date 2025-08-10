<?php
// api/get_user_data.php
// This script fetches the current logged-in user's data.

include 'db_connection.php';
session_start();
header('Content-Type: application/json');

// Check if the user is logged in by checking the session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare a statement to get username and interests
$stmt = $conn->prepare("SELECT username, interests FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Process interests from a comma-separated string into an array
    $interests_array = [];
    if (!empty($user['interests'])) {
        $interests_array = array_map('trim', explode(',', $user['interests']));
    }

    echo json_encode([
        'success' => true,
        'username' => $user['username'],
        'interests' => $interests_array
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found.']);
}

$stmt->close();
$conn->close();
?>
