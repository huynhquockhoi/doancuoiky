<?php
require_once '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kiểm tra dữ liệu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_products']) && is_array($_POST['selected_products'])) {
    $selected_products = array_map('intval', $_POST['selected_products']);
    
    if (count($selected_products) > 0) {
        // Lấy thông tin sản phẩm trước khi xóa (để xóa file ảnh)
        $ids = implode(',', array_fill(0, count($selected_products), '?'));
        $img_sql = "SELECT id, image FROM products WHERE id IN ($ids)";
        $img_stmt = $conn->prepare($img_sql);
        
        // Bind các tham số là ID sản phẩm
        $types = str_repeat('i', count($selected_products));
        $img_stmt->bind_param($types, ...$selected_products);
        $img_stmt->execute();
        $images_result = $img_stmt->get_result();
        
        // Lưu thông tin ảnh cần xóa
        $images = [];
        while ($row = $images_result->fetch_assoc()) {
            if (!empty($row['image'])) {
                $images[] = $row['image'];
            }
        }
        
        // Xóa sản phẩm trong database
        $delete_sql = "DELETE FROM products WHERE id IN ($ids)";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param($types, ...$selected_products);
        
        if ($delete_stmt->execute()) {
            // Xóa các file ảnh
            foreach ($images as $image) {
                $image_path = '../uploads/' . $image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $_SESSION['success_message'] = 'Xóa ' . count($selected_products) . ' sản phẩm thành công';
        } else {
            $_SESSION['error_message'] = 'Có lỗi xảy ra khi xóa sản phẩm';
        }
    }
}

// Chuyển hướng về trang quản lý sản phẩm
header('Location: index.php');
exit;
?>