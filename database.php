<?php
$host = "monorail.proxy.rlwy.net"; // use external hostname, not localhost
$port = 3306; // or the actual port if Railway shows something like 55696
$dbname = "login_register";
$username = "root";
$password = "JiRyTsiTeKxHbWbtlSXbtFhRJvGASZod";

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to Railway MySQL!";
?>