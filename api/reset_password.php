<?php
// api/reset_password.php
// This script handles the password reset process.
include 'db_connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];
$new_password = $data['password'];

if (empty($token) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Token and new password are required.']);
    exit();
}

// You could add password strength validation here as well
if (strlen($new_password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit();
}

$hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE recovery_token = ?");
$stmt->bind_param("ss", $hashed_password, $token);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Password has been reset successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid token.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
}
$stmt->close();
$conn->close();
?>
