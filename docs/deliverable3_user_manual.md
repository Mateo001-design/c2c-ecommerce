# Deliverable 3 – User Manual
## iTradeZA C2C E-Commerce Platform

---

## Technical Stack

| Technology | Version | Purpose |
|---|---|---|
| **HTML5** | 5 | Page structure and semantic markup |
| **CSS3** | 3 | Styling, animations, responsive design |
| **Bootstrap** | 5.3.3 | Responsive grid system, components, utilities |
| **JavaScript** | ES6 | Client-side interactivity, form validation |
| **jQuery** | 3.7.1 | DOM manipulation, AJAX, event handling |
| **PHP** | 8.x | Server-side logic, authentication, CRUD operations |
| **MySQL** | 8.x | Relational database for all persistent data |
| **Bootstrap Icons** | 1.11.3 | Icon library for UI elements |
| **Apache** | 2.4+ | Web server with .htaccess support |
| **PDO** | – | PHP Data Objects for secure database access |

---

## System Features

### Customer-Facing Features

| Feature | Description |
|---|---|
| **User Registration** | Create a buyer or seller account with username, email, password, phone, city, and province. |
| **User Login/Logout** | Secure session-based authentication with bcrypt password hashing. |
| **Browse Products** | View all active listings with category filters, price range, condition filter, and sorting options. |
| **Search** | Full-text search across product titles and descriptions. |
| **Product Details** | View full product information, seller profile, ratings, and multiple images. |
| **Shopping Cart** | Add products, update quantities, remove items, view running total in ZAR. |
| **Checkout** | Place an order with shipping address and choice of payment method (EFT, e-Wallet, Cash on Delivery, Card). |
| **My Orders** | View all placed orders with status tracking (Pending → Paid → Shipped → Delivered). |
| **Sell Products** | List items for sale with title, description, price (ZAR), category, condition, images, and location. |
| **My Sales** | Manage listings and received orders; update order statuses. |
| **Messaging** | Real-time conversation threads between buyers and sellers, linked to specific products. |
| **Reviews & Ratings** | Rate sellers (1–5 stars) with comments after a delivered order. |
| **User Profile** | Update personal information, change password, upload profile picture. |

### Admin Features (RBAC)

| Feature | Description |
|---|---|
| **Dashboard** | Overview of platform statistics: total users, products, orders, revenue. |
| **Manage Users** | Create, Read, Update, Delete users. Search and filter by role. Change user roles and verification status. |
| **Manage Products** | View all product listings. Update product status (active/sold/inactive). Edit or delete any product. |
| **Manage Orders** | View all orders across the platform. Update order statuses. Filter by status. |
| **Manage Roles (RBAC)** | Create new roles, update role descriptions, delete unused roles. View user counts per role. |

---

## Operational Guide

### 1. Getting Started – Registration

1. Open the iTradeZA website in your browser.
2. Click **"Register"** in the top navigation bar.
3. Fill in the registration form:
   - **First Name** and **Last Name** (required)
   - **Username** (minimum 3 characters, unique)
   - **Email Address** (valid email, unique)
   - **Password** (minimum 6 characters) and **Confirm Password**
   - **Phone** (optional)
   - **Account Type**: Choose **Buyer** or **Seller**
   - **City** and **Province** (optional)
4. Click **"Create Account"**.
5. You will be redirected to the login page with a success message.

### 2. Logging In

1. Click **"Login"** in the navigation bar.
2. Enter your **Email Address** and **Password**.
3. Click **"Login"**.
4. Upon successful login, you will be redirected to the home page.

### 3. Browsing & Searching Products

1. Click **"Browse"** in the navigation bar or use the **search bar**.
2. Use the **left sidebar filters** to narrow results:
   - **Category**: Select a product category (Electronics, Fashion, etc.)
   - **Price Range**: Set minimum and maximum price in ZAR
   - **Condition**: Filter by New, Used, or Refurbished
   - **Sort By**: Order results by Newest, Price Low→High, Price High→Low, or Oldest
3. Click **"Apply Filters"** to update results.
4. Click **"View"** on any product card to see full details.

### 4. Viewing a Product

1. The product page shows:
   - **Product images** (click thumbnails to switch main image)
   - **Title, price** (in ZAR), **condition**, and **category**
   - **Description** and **quantity available**
   - **Seller information** with average rating
2. Click **"Add to Cart"** to add the item to your shopping cart.
3. Click **"Message Seller"** to start a conversation about the product.

### 5. Managing Your Cart

1. Click the **Cart icon** in the navigation bar.
2. The cart page shows all added items with:
   - Product name and image
   - Unit price and quantity
   - Subtotal per item
3. **Update quantity**: Change the number and click the refresh button.
4. **Remove item**: Click the red trash icon.
5. **Clear cart**: Click "Clear Cart" to empty everything.
6. **Proceed to Checkout**: Click the green checkout button.

### 6. Checkout & Placing an Order

1. Review the **Order Summary** on the left.
2. Enter your **Shipping Address** on the right (pre-filled from your profile).
3. Select a **Payment Method**:
   - EFT / Bank Transfer
   - Cash on Delivery
   - e-Wallet (Capitec Pay / FNB)
   - Credit/Debit Card
4. Click **"Place Order"**.
5. You will be redirected to **My Orders** with a confirmation message.

### 7. Tracking Orders

1. Go to **Profile dropdown → My Orders**.
2. View all orders with status badges:
   - **Pending** (yellow) – Awaiting payment/confirmation
   - **Paid** (blue) – Payment received
   - **Shipped** (blue) – Item dispatched
   - **Delivered** (green) – Order completed
   - **Cancelled** (red) – Order cancelled
3. Click the **eye icon** to expand and see individual items in an order.
4. For delivered orders, click **"Leave Review"** to rate the seller.

### 8. Selling Products (Seller Account)

1. Click **"Sell"** in the navigation bar (visible to sellers only).
2. Fill in the product listing form:
   - **Product Title** (max 150 characters)
   - **Description** (detailed text)
   - **Price** in ZAR
   - **Category** (select from dropdown)
   - **Condition** (New / Used / Refurbished)
   - **Quantity** available
   - **Location** (e.g., "Soweto, Gauteng")
   - **Product Images** (up to 5, max 5MB each; first image is the main photo)
3. Click **"List Product"**.

### 9. Managing Sales (Seller Account)

1. Go to **Profile dropdown → My Sales**.
2. The page has two sections:
   - **My Listings**: All your products with edit/delete options.
   - **Orders Received**: Orders from buyers with status controls.
3. To update an order status, select the new status from the dropdown and click **"Update"**.

### 10. Messaging

1. To message a seller: Click **"Message Seller"** on any product page.
2. To view all conversations: Click **"Messages"** in the navigation bar.
3. The messaging interface shows:
   - **Contact list** on the left (sorted by most recent)
   - **Active conversation** on the right with message history
4. Type your message and click the **Send** button (or press Enter).
5. Messages are displayed in chat-bubble format (green = sent, grey = received).

### 11. Leaving a Review

1. After an order is delivered, go to **My Orders**.
2. Expand the order and click **"Leave Review"**.
3. Select a **Rating** (1–5 stars) and optionally add a **Comment**.
4. Click **"Submit Review"**.

### 12. Updating Your Profile

1. Go to **Profile dropdown → Profile**.
2. Update any of: first name, last name, phone, city, province, address.
3. Upload a new **Profile Picture** (JPEG, PNG, GIF, or WebP, max 5MB).
4. Change your **Password** by entering a new one (leave blank to keep current).
5. Click **"Save Changes"**.

---

## Admin Operational Guide

### Accessing the Admin Panel

1. Log in with an admin account.
2. Click **Profile dropdown → Admin Panel**.

### Admin Dashboard

The dashboard provides an at-a-glance overview:
- **Total Users** (blue card)
- **Total Products** (green card)
- **Total Orders** (yellow card)
- **Revenue** (blue info card)
- **Recent Orders** table (last 5)
- **New Users** table (last 5)

### Managing Users (CRUD)

1. Navigate to **Users** in the admin sidebar.
2. **Search** users by username, email, or name.
3. **Filter** by role (Admin, Seller, Buyer).
4. **Create User**: Click "Create User" button → fill in form → submit.
5. **Edit User**: Click pencil icon → change role and/or verification status → save.
6. **Delete User**: Click trash icon (cannot delete yourself).

### Managing Products (CRUD)

1. Navigate to **Products** in the admin sidebar.
2. **Search** by product title or seller username.
3. **Update Status**: Change dropdown (Active/Sold/Inactive) → click check icon.
4. **Edit Product**: Click pencil icon → redirects to edit form.
5. **Delete Product**: Click trash icon (with confirmation).

### Managing Orders (CRUD)

1. Navigate to **Orders** in the admin sidebar.
2. **Filter** by status (Pending, Paid, Shipped, Delivered, Cancelled).
3. **Update Status**: Change dropdown → click check icon.

### Managing Roles – RBAC

1. Navigate to **Roles** in the admin sidebar.
2. **View Roles**: Table shows all roles with descriptions and user counts.
3. **Create Role**: Fill in the form on the right → click "Create Role".
4. **Update Role**: Edit the description inline → click check icon.
5. **Delete Role**: Only possible if no users are assigned to that role.
6. **Access Levels**:
   - **Admin**: Full CRUD on all users, products, orders, and roles.
   - **Seller**: Create/edit own listings, manage received orders, message buyers.
   - **Buyer**: Browse, purchase, review sellers, message sellers.

---

## Default Login Credentials

| Role | Email | Password |
|---|---|---|
| Admin | admin@itradeza.co.za | Admin@123 |

*Note: Change the admin password immediately after first login in a production environment.*

---

## Troubleshooting

| Issue | Solution |
|---|---|
| Cannot register | Ensure username and email are unique; password is 6+ characters. |
| Cannot see "Sell" button | Only seller accounts can list products. Check your account type. |
| Images not uploading | Ensure files are JPEG/PNG/GIF/WebP and under 5MB. |
| Cart not updating | Refresh the page; ensure JavaScript is enabled in your browser. |
| Admin panel not accessible | Only accounts with the "admin" role can access the admin panel. |
