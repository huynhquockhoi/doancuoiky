<?php
require_once 'includes/header.php';

// Lấy danh sách sản phẩm từ database
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart']) && isLoggedIn()) {
    $product_id = (int)$_POST['product_id'];
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Cập nhật số lượng nếu đã có trong giỏ hàng
        $cart_item = $check_result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + 1;
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $update_stmt->execute();
    } else {
        // Thêm mới vào giỏ hàng
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $product_id);
        $insert_stmt->execute();
    }
    
    // Chuyển hướng để tránh gửi lại form khi refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Sản phẩm quần áo</h1>
        <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info">
            Vui lòng <a href="login.php">đăng nhập</a> để mua hàng.
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="col-md-4">
                <div class="card product-card">
                    <img src="<?= !empty($product['image']) ? 'uploads/' . htmlspecialchars($product['image']) : 'https://via.placeholder.com/300x200?text=No+Image' ?>" 
                         class="card-img-top product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                        <p class="card-text text-danger font-weight-bold"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</p>
                        
                        <?php if (isLoggedIn()): ?>
                        <form method="post">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-primary btn-block">Thêm vào giỏ</button>
                        </form>
                        <?php else: ?>
                        <a href="login.php" class="btn btn-secondary btn-block">Đăng nhập để mua</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-warning">Không có sản phẩm nào.</div>
        </div>
    <?php endif; ?>
</div>
<?php
echo "<h1>🧺 Welcome to the Clothing Store App!</h1>";
echo "<p>PHP & Apache are working correctly via Docker 🚀</p>";
?>

<?php include 'includes/footer.php'; ?>