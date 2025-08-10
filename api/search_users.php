<?php
// api/search_users.php
include 'db_connection.php';
header('Content-Type: application/json');

$query = $_GET['query'] ?? '';

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'users' => []]); // Return empty if query is too short
    exit();
}

$users = [];
$search_term = "%{$query}%";

$stmt = $conn->prepare("SELECT username, credential_points FROM users WHERE username LIKE ? LIMIT 10");
$stmt->bind_param("s", $search_term);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode(['success' => true, 'users' => $users]);

$stmt->close();
$conn->close();
?>