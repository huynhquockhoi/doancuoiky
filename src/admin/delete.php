<?php
require_once '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$product_id = (int)$_GET['id'];

// Lấy thông tin sản phẩm trước khi xóa (để xóa file ảnh nếu có)
$sql = "SELECT image FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    
    // Xóa sản phẩm trong database
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $product_id);
    
    if ($delete_stmt->execute()) {
        // Xóa file ảnh nếu có
        if (!empty($product['image'])) {
            $image_path = '../uploads/' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $_SESSION['success_message'] = 'Xóa sản phẩm thành công';
    } else {
        $_SESSION['error_message'] = 'Có lỗi xảy ra khi xóa sản phẩm';
    }
}

// Chuyển hướng về trang quản lý sản phẩm
header('Location: index.php');
exit;
?>