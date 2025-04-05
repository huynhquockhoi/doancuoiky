<?php
require_once 'includes/header.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Xử lý cập nhật số lượng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        $quantity = max(1, (int)$quantity); // Đảm bảo số lượng tối thiểu là 1
        
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        $update_stmt->execute();
    }
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['remove_item'])) {
    $cart_id = (int)$_POST['cart_id'];
    
    $delete_sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $cart_id, $user_id);
    $delete_stmt->execute();
}

// Xử lý thanh toán
if (isset($_POST['checkout'])) {
    // Đối với demo, chỉ xóa giỏ hàng
    $clear_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_stmt = $conn->prepare($clear_sql);
    $clear_stmt->bind_param("i", $user_id);
    
    if ($clear_stmt->execute()) {
        $_SESSION['checkout_success'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Lấy sản phẩm trong giỏ hàng
$cart_sql = "SELECT c.id, c.quantity, p.id as product_id, p.name, p.price, p.image 
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id = ?";
$cart_stmt = $conn->prepare($cart_sql);
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
?>

<div class="row">
    <div class="col-md-12">
        <h1 class="mb-4">Giỏ hàng của bạn</h1>
        
        <?php if (isset($_SESSION['checkout_success'])): ?>
            <div class="alert alert-success">
                Thanh toán thành công! Cảm ơn bạn đã mua hàng.
            </div>
            <?php unset($_SESSION['checkout_success']); ?>
        <?php endif; ?>
        
        <?php if ($cart_result->num_rows > 0): ?>
            <form method="post">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Hình ảnh</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        while ($item = $cart_result->fetch_assoc()): 
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>
                                    <img src="<?= !empty($item['image']) ? 'uploads/' . htmlspecialchars($item['image']) : 'https://via.placeholder.com/80x60?text=No+Image' ?>" 
                                        width="80" height="60" alt="<?= htmlspecialchars($item['name']) ?>">
                                </td>
                                <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <input type="number" name="quantity[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" 
                                           min="1" max="99" class="form-control" style="width: 70px;">
                                </td>
                                <td><?= number_format($item_total, 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="remove_item" class="btn btn-sm btn-danger">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Tổng tiền:</th>
                            <th><?= number_format($total, 0, ',', '.') ?> VNĐ</th>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" name="update_cart" class="btn btn-primary">
                            Cập nhật giỏ hàng
                        </button>
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="submit" name="checkout" class="btn btn-success">
                            Thanh toán
                        </button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">
                Giỏ hàng của bạn trống. <a href="index.php">Tiếp tục mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>