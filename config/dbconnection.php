<?php
$conn = new mysqli('localhost', 'root', '', 'evoting_db');

// Check connection properly
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4 for proper encoding and emoji support
$conn->set_charset("utf8mb4");
?>