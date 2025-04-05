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
$error = '';
$success = '';

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$product = $result->fetch_assoc();

// Xử lý cập nhật sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    
    // Kiểm tra dữ liệu
    if (empty($name)) {
        $error = 'Vui lòng nhập tên sản phẩm';
    } elseif ($price <= 0) {
        $error = 'Giá sản phẩm phải lớn hơn 0';
    } else {
        // Xử lý upload hình ảnh mới (nếu có)
        $image_name = $product['image']; // Giữ nguyên ảnh cũ nếu không có ảnh mới
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            
            // Tạo thư mục upload nếu chưa tồn tại
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Tạo tên file duy nhất để tránh trùng lặp
            $file_name = basename($_FILES['image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;
            
            // Chỉ cho phép upload ảnh
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Xóa ảnh cũ nếu có
                    if (!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                        unlink($upload_dir . $product['image']);
                    }
                    $image_name = $new_file_name;
                } else {
                    $error = 'Không thể upload file';
                }
            } else {
                $error = 'Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)';
            }
        }
        
        if (empty($error)) {
            // Cập nhật sản phẩm vào database
            $sql = "UPDATE products SET name = ?, description = ?, price = ?, image = ?, stock = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsii", $name, $description, $price, $image_name, $stock, $product_id);
            
            if ($stmt->execute()) {
                $success = 'Cập nhật sản phẩm thành công';
                // Cập nhật lại thông tin sản phẩm
                $product['name'] = $name;
                $product['description'] = $description;
                $product['price'] = $price;
                $product['image'] = $image_name;
                $product['stock'] = $stock;
            } else {
                $error = 'Có lỗi xảy ra: ' . $conn->error;
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Chỉnh sửa sản phẩm</h1>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Tên sản phẩm</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Giá (VNĐ)</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Số lượng trong kho</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Hình ảnh hiện tại</label>
                        <?php if (!empty($product['image'])): ?>
                            <div class="mb-2">
                                <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" width="150" alt="<?= htmlspecialchars($product['name']) ?>">
                            </div>
                        <?php else: ?>
                            <div class="text-muted mb-2">Chưa có hình ảnh</div>
                        <?php endif; ?>
                        
                        <input type="file" class="form-control-file" id="image" name="image">
                        <small class="form-text text-muted">Để trống nếu không muốn thay đổi hình ảnh</small>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Cập nhật sản phẩm</button>
                        <a href="index.php" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>