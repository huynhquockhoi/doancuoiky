<?php
require_once '../includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Xử lý thêm sản phẩm mới
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
        // Xử lý upload hình ảnh
        $image_name = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = basename($_FILES['image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_file_name = uniqid() . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $image_name = $new_file_name;
                } else {
                    $error = 'Không thể upload file';
                }
            } else {
                $error = 'Chỉ chấp nhận file ảnh (JPG, JPEG, PNG, GIF)';
            }
        }

        if (empty($error)) {
            // Thêm sản phẩm vào database (bỏ category_id)
            $sql = "INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssdsi", $name, $description, $price, $image_name, $stock);

            if ($stmt->execute()) {
                $success = 'Thêm sản phẩm thành công';
                $name = $description = '';
                $price = $stock = 0;
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
        <h1 class="mb-4">Thêm sản phẩm mới</h1>

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
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($description ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price">Giá (VNĐ)</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price ?? 0) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Số lượng trong kho</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($stock ?? 0) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Hình ảnh</label>
                        <input type="file" class="form-control-file" id="image" name="image">
                        <small class="form-text text-muted">Chọn file ảnh (JPG, JPEG, PNG, GIF)</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Thêm sản phẩm</button>
                        <a href="index.php" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
