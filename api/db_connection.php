<?php
// api/db_connection.php
$servername = "localhost";
$username = "root"; // Your DB username
$password = "";     // Your DB password
$dbname = "intportal"; // Your DB name
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>