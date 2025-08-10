<?php
// api/login.php - UPDATED
// Now checks for interests and returns them.
include 'db_connection.php';
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit();
}

// Fetch id, password, and interests
$stmt = $conn->prepare("SELECT id, password, interests FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password, $interests);
    $stmt->fetch();
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        // Return success, username, and whether interests are set
        echo json_encode([
            'success' => true,
            'username' => $username,
            'interests_set' => !empty($interests)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user found with that username.']);
}
$stmt->close();
$conn->close();
?>