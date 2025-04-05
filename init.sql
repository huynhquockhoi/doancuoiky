-- Tạo bảng người dùng
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
    password VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    is_admin BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Tạo bảng sản phẩm quần áo
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    description TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, 
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT
);

-- Thêm trường category_id vào bảng products
ALTER TABLE products ADD COLUMN category_id INT;
ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id);

-- Tạo bảng giỏ hàng
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Thêm tài khoản admin 
INSERT INTO users (username, password, is_admin) VALUES ('admin', '$2y$10$8MNjGMFCiQnNGzqcDWfS6eRGS/hDNHDv8hC0gxC8zrWzyt3vVpAce', 1);

-- Thêm một số sản phẩm 
INSERT INTO products (name, description, price, image, stock) VALUES 
('Áo thun nam', 'Áo thun nam chất liệu cotton cao cấp', 150000, 'tshirt.jpg', 100),
('Áo sơ mi nữ', 'Áo sơ mi nữ kiểu dáng công sở', 250000, 'blouse.jpg', 80),
('Quần jean nam', 'Quần jean nam form slim fit', 350000, 'jeans.jpg', 50),
('Váy đầm dạ hội', 'Váy đầm dạ hội cao cấp', 550000, 'dress.jpg', 30),
('Áo khoác denim', 'Áo khoác denim unisex', 450000, 'jacket.jpg', 45);
-- Tạo bảng categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id INT;

ALTER TABLE products ADD CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id);

INSERT INTO categories (name, description) VALUES
('Áo', 'Các loại áo thời trang'),
('Quần', 'Các loại quần thời trang'),
('Váy', 'Các loại váy thời trang'),
('Phụ kiện', 'Các loại phụ kiện thời trang');