 
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  description TEXT,
  category VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  image_url VARCHAR(500) DEFAULT NULL,
  stock INT UNSIGNED NOT NULL DEFAULT 20,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cart_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  UNIQUE KEY unique_user_product (user_id, product_id),
  CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Placed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  product_name VARCHAR(180) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT UNSIGNED NOT NULL,
  CONSTRAINT fk_orderitem_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_orderitem_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO products (name, description, category, price, image_url, stock) VALUES
('Wireless Headphones', 'Comfortable wireless headphones for everyday listening.', 'Electronics', 1499.00, 'https://images.unsplash.com/photo-1505756714330-6e9c5b7f1f1d?auto=format&fit=crop&w=800&q=80', 25),
('Smart Watch', 'A stylish smart watch for fitness and notifications.', 'Electronics', 2299.00, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=800&q=80', 18),
('Smartphone', 'Feature-packed smartphone for daily use.', 'Mobiles', 12999.00, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=800&q=80', 12),
('Running Shoes', 'Lightweight shoes for walking and running.', 'Fashion', 1899.00, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80', 30),
('Backpack', 'Everyday backpack with multiple compartments.', 'Fashion', 999.00, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80', 22),
('Desk Lamp', 'Modern desk lamp for study and work.', 'Home', 799.00, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=800&q=80', 15),
('Coffee Mug', 'Ceramic mug for tea and coffee.', 'Home', 299.00, 'https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=800&q=80', 40),
('Notebook Set', 'Notebook set for school, college, and office.', 'Books & Stationery', 249.00, 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?auto=format&fit=crop&w=800&q=80', 50)
ON DUPLICATE KEY UPDATE name = VALUES(name);
