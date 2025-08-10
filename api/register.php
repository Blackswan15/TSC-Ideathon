<?php
// api/register.php
include 'db_connection.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];
if (empty($username) || strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    exit();
}
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already taken.']);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$recovery_token = bin2hex(random_bytes(16));
$stmt = $conn->prepare("INSERT INTO users (username, password, recovery_token) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $recovery_token);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'token' => $recovery_token]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed.']);
}
$stmt->close();
$conn->close();
?>
