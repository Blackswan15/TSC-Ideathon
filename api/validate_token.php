<?php
// api/validate_token.php
// This script validates the recovery token provided by the user.
include 'db_connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Token is required.']);
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE recovery_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid token.']);
}
$stmt->close();
$conn->close();
?>
