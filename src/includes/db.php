<?php
session_start();

$host = getenv('DB_HOST') ?: 'db';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password123';
$dbname = getenv('DB_NAME') ?: 'clothing_store';

// Tạo kết nối
$conn = new mysqli($host, $user, $password, $dbname);

// Kiểm tra kết nối  
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập charset
$conn->set_charset("utf8mb4");
?>