<?php
session_start();

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$dbname = getenv('DB_NAME') ?: 'clothing_store';

// Chuyển sang PostgreSQL nếu dùng Render
$conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);