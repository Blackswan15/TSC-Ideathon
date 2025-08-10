
<?php
// api/check_username.php
include 'db_connection.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
if (strlen($username) < 3) {
    echo json_encode(['available' => false]);
    exit();
}
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
echo json_encode(['available' => $stmt->num_rows === 0]);
$stmt->close();
$conn->close();
?>