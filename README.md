# iTradeZA – C2C E-Commerce Platform

A Consumer-to-Consumer (C2C) e-commerce platform built for South Africa's informal and township economy. Enables individuals to buy and sell goods directly, with secure transactions, seller verification, and ZAR-denominated pricing.

## Tech Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript, jQuery
- **Backend:** PHP 8, PDO (MySQL)
- **Database:** MySQL 8
- **Icons:** Bootstrap Icons 1.11.3

## Features

### Customer Site
- User registration & authentication (buyer/seller accounts)
- Product listing with images, categories, pricing in ZAR
- Browse & search with filters (category, price range, condition, sort)
- Shopping cart & checkout (EFT, e-Wallet, Cash on Delivery, Card)
- Buyer–seller messaging system
- Order tracking with status updates
- Seller ratings & reviews (1–5 stars)
- User profile management

### Admin Panel (RBAC)
- Dashboard with platform statistics
- Full CRUD on Users, Products, Orders
- Role-Based Access Control: create/edit/delete roles
- Seller verification management

## Project Structure

```
├── index.php              # Home page
├── config/database.php    # DB connection & site config
├── includes/              # Shared header, footer, helpers
├── auth/                  # Login, register, logout
├── products/              # Browse, view, add, edit, delete
├── cart/                  # Cart & checkout
├── orders/                # My orders, my sales, reviews
├── messages/              # Buyer-seller messaging
├── profile/               # User profile
├── admin/                 # Admin panel (dashboard, users, products, orders, roles)
├── assets/css/style.css   # Custom styles
├── assets/js/main.js      # Client-side JavaScript
├── sql/database.sql       # MySQL schema
├── uploads/               # User-uploaded images
└── docs/                  # Project documentation (deliverables)
```

## Setup Instructions

### Prerequisites
- PHP 8.0+ with PDO MySQL and GD extensions
- MySQL 8.0+
- Apache web server with mod_rewrite

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Mateo001-design/c2c-ecommerce.git
   cd c2c-ecommerce
   ```

2. **Create the database:**
   ```bash
   mysql -u root -p < sql/database.sql
   ```

3. **Configure the database connection:**
   Edit `config/database.php` and update:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'itradeza_c2c');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('SITE_URL', 'http://your-domain.com');
   ```

4. **Set permissions:**
   ```bash
   chmod -R 755 uploads/
   ```

5. **Deploy to web server** (or use PHP built-in server for testing):
   ```bash
   php -S localhost:8000
   ```

### Default Admin Login
- **Email:** admin@itradeza.co.za
- **Password:** Admin@123

## Documentation

- [Deliverable 1 – Project Proposal](docs/deliverable1_proposal.md)
- [Deliverable 2 – Design & Prototype](docs/deliverable2_design.md)
- [Deliverable 3 – User Manual](docs/deliverable3_user_manual.md)

## License

This project is developed for educational purposes.
