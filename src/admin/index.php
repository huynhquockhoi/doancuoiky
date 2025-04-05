<?php
require_once '../includes/db.php';

// Kiểm tra quyền admin (đối với demo, chúng ta cho phép mọi người dùng đã đăng nhập đều có quyền admin)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Lấy danh sách sản phẩm từ database
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

include '../includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1>Quản lý sản phẩm</h1>
    </div>
    <div class="col-md-6 text-right">
        <a href="add.php" class="btn btn-success">Thêm sản phẩm mới</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Mô tả</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($product = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td>
                                    <img src="<?= !empty($product['image']) ? '../uploads/' . htmlspecialchars($product['image']) : 'https://via.placeholder.com/80x60?text=No+Image' ?>" 
                                         width="80" height="60" alt="<?= htmlspecialchars($product['name']) ?>">
                                </td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars(substr($product['description'], 0, 100)) . (strlen($product['description']) > 100 ? '...' : '') ?></td>
                                <td><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</td>
                                <td><?= $product['stock'] ?></td>
                                <td>
                                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                                    <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có sản phẩm nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<form method="post" action="delete_multiple.php">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_products[]" value="<?= $product['id'] ?>"></td>
                        <td><?= $product['id'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa các sản phẩm đã chọn?')">
        Xóa sản phẩm đã chọn
    </button>
</form>
<?php include '../includes/footer.php'; ?>