
<?php
// api/save_interests.php - NEW SCRIPT
// Saves the user's selected interests to the database.
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$interests = $data['interests']; // Expecting an array of interests

if (empty($interests) || count($interests) !== 3) {
    echo json_encode(['success' => false, 'message' => 'Please select exactly 3 interests.']);
    exit();
}

// Convert array to a comma-separated string for storage
$interests_str = implode(',', $interests);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE users SET interests = ? WHERE id = ?");
$stmt->bind_param("si", $interests_str, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save interests.']);
}

$stmt->close();
$conn->close();
?>