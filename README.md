# ShopKart — PHP & MySQL E-commerce Project

A Flipkart-inspired student shopping website built with HTML, CSS, JavaScript, PHP and MySQL.

## Features
- Registration and login with `password_hash()` / `password_verify()`
- Session-based authentication and logout
- Product catalogue, category filters and search
- Product detail pages
- MySQL-backed cart with quantity updates and remove
- Checkout that creates an order and reduces stock in a transaction
- User dashboard and order history
- Prepared statements, HTML output escaping, and CSRF tokens for forms

## Requirements
- XAMPP (Apache + MySQL + PHP 8+ recommended)
- VS Code or another editor
- Browser

## Setup
1. Copy the `shopkart` folder to `C:\xampp\htdocs\shopkart`.
2. Start **Apache** and **MySQL** from XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database.sql`, and run the import.
5. Check `config/db.php`. Default XAMPP settings are host `127.0.0.1`, user `root`, blank password. Change these if your MySQL setup differs.
6. Visit `http://localhost/shopkart/`.
7. Register a new account and test the shop.

## Main pages
- `index.php` — homepage
- `register.php`, `login.php`, `logout.php` — authentication
- `products.php`, `product.php` — catalogue and details
- `cart.php`, `checkout.php` — cart and demo order placement
- `dashboard.php`, `orders.php` — user area

## Notes
- This project does not take real payments or collect shipping addresses.
- Product images use external image URLs; internet access is required to display them. Replace them with local images if desired.
- This is an educational starter project. Before production use, add rate limiting, email verification, password reset, shipping address validation, an actual payment provider, deployment secrets, logging/monitoring, and a security review.
