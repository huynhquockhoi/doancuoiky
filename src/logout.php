<?php
require_once 'includes/db.php';

// Xóa tất cả thông tin phiên
session_unset();
session_destroy();

// Chuyển hướng về trang chủ
header('Location: index.php');
exit;
?>