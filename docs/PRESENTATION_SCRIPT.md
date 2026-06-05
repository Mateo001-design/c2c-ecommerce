# Deliverable 3 — Presentation Script & Notes

## Presentation Structure (15 marks)
**Duration:** 10–15 minutes  
**Format:** Live demo of hosted website + Q&A

---

## Opening (1 minute)

> "Good day, my name is [Your Name]. Today I'll be presenting **iTradeZA**, a Consumer-to-Consumer e-commerce platform designed specifically for the South African market. The platform enables individuals — particularly those in the informal/township economy — to buy and sell goods online securely."

> "iTradeZA was built using HTML, CSS, JavaScript, PHP, and MySQL, and is fully responsive across all device sizes."

---

## Part 1: Platform Overview (2 minutes)

**Show the Home Page:**
- Point out the hero banner with "Buy & Sell Across South Africa"
- Show the live stats bar (Active Listings, Registered Users, Completed Trades)
- Show the 10 product categories
- Mention the "How iTradeZA Works" section at the bottom

> "The home page immediately communicates the platform's purpose and provides easy entry points for both buyers and sellers."

---

## Part 2: Buyer Flow Demo (3 minutes)

**Step 1: Registration**
- Click "Register"
- Show the form fields (emphasise SA provinces dropdown, account type selector)
- Complete registration as a Buyer

> "Notice the South African localisation — we have all 9 provinces in the dropdown, and users can choose to register as either a Buyer or Seller."

**Step 2: Browse & Search**
- Navigate to "Browse Products"
- Show the filter sidebar (Category, Price Range, Condition, Sort)
- Demonstrate search functionality

> "The browse page supports filtering by category, price range, and condition. Results are paginated for performance."

**Step 3: Add to Cart & Checkout**
- Click on a product
- Click "Add to Cart"
- Go to Cart, show the items
- Click "Proceed to Checkout"
- Fill in shipping address
- Show payment methods: EFT, Cash on Delivery, e-Wallet, Credit/Debit Card

> "We support four South African payment methods. The checkout stores shipping address and creates an order with 'Pending' status."

**Step 4: Messages**
- Go back to a product
- Click "Message Seller"
- Send a message
- Show the chat bubble interface

> "Buyers can communicate directly with sellers through an integrated messaging system with real-time chat bubbles."

---

## Part 3: Seller Flow Demo (2 minutes)

**Step 1: Login as Seller**
- Show the "Sell" link in nav (only visible to sellers)
- Click "Sell" to list a product
- Fill in: Title, Description, Price (in Rands), Category, Condition, Quantity, Location
- Show image upload capability (up to 5 images)

> "Sellers can list products with multiple images, specify condition and location, and set prices in South African Rands."

**Step 2: My Sales**
- Navigate to "My Sales"
- Show listings table with edit/delete options
- Show "Orders Received" with status dropdown
- Change an order status (e.g., Pending → Shipped)

> "The My Sales page gives sellers full visibility into their listings and orders, with the ability to update order status as items move through fulfilment."

---

## Part 4: Admin Panel Demo (3 minutes)

**Step 1: Dashboard**
- Login as admin (admin@itradeza.co.za)
- Click "Admin Panel"
- Show the 4 stats cards: Users, Products, Orders, Revenue
- Point out the dark-themed design (separate from main site)

> "The admin panel provides a dashboard with key platform metrics at a glance."

**Step 2: User Management (CRUD)**
- Navigate to Users
- Show the users table with roles displayed as colour-coded badges
- Click "Create User" and show the modal
- Edit a user's role
- Demonstrate delete functionality

> "Full CRUD operations on users — create, read, update, delete. Notice the role-based badges: Admin in red, Seller in green, Buyer in blue."

**Step 3: Role-Based Access Control (RBAC)**
- Navigate to Roles
- Show the three default roles: Admin, Seller, Buyer
- Show user count per role
- Create a new role (e.g., "Moderator")
- Delete it

> "The RBAC system allows administrators to define custom roles. Each role controls what features a user can access. For example, only sellers see the 'Sell' link, and only admins can access this panel."

**Step 4: Orders & Products Management**
- Quickly show the Orders page (status updates, filters)
- Show the Products page (admin can manage all listings)

---

## Part 5: Technical Highlights (2 minutes)

> "Let me briefly highlight some technical aspects:"

1. **Security:** "Passwords are hashed with bcrypt using PHP's `password_hash()`. All SQL queries use prepared statements to prevent SQL injection. User inputs are sanitised with `htmlspecialchars()` to prevent XSS."

2. **Responsive Design:** "The entire platform is built mobile-first with Bootstrap 5. Every page works on smartphones, tablets, and desktops."

3. **Database Design:** "The MySQL database has 10 normalised tables with foreign key constraints, ENUM fields for status validation, and CHECK constraints on review ratings."

4. **Role-Based Access Control:** "Every admin route calls `requireAdmin()` which checks the user's role before allowing access. This prevents unauthorised access even if someone knows the URL."

---

## Closing (1 minute)

> "In summary, iTradeZA is a complete C2C e-commerce platform that addresses the need for a locally-focused, digitised marketplace for South African consumers and informal traders. It supports the full trade lifecycle — from listing to checkout to delivery tracking — with secure authentication and administrative controls."

> "Thank you. I'm happy to answer any questions."

---

## Likely Questions & Answers

| Question | Answer |
|----------|--------|
| "How do you prevent SQL injection?" | "All database queries use PDO prepared statements with parameterised values. No raw user input ever touches a SQL query." |
| "How does RBAC work?" | "Each user has a `role_id` that maps to the `roles` table. PHP functions like `requireAdmin()` check this role before page access. The role controls which nav links appear and which pages are accessible." |
| "What payment methods are supported?" | "EFT/Bank Transfer, Cash on Delivery, e-Wallet, and Credit/Debit Card. These are the most common payment methods in South Africa." |
| "Is it mobile responsive?" | "Yes, built mobile-first with Bootstrap 5. The navigation collapses to a hamburger menu, grids adjust from 3 columns to 1 column, and all forms are full-width on mobile." |
| "How do you handle passwords?" | "PHP's `password_hash()` with bcrypt algorithm. On login, `password_verify()` compares the submitted password against the stored hash." |
| "What database engine did you use?" | "MySQL 8.0 with InnoDB engine for transaction support and foreign key constraints." |
| "How many tables are in the database?" | "10 tables: roles, users, categories, products, product_images, cart, orders, order_items, messages, and reviews." |
| "Can sellers see buyer orders?" | "Sellers can only see orders placed for THEIR products via the 'My Sales' page. They cannot see other sellers' orders." |
| "How does messaging work?" | "Buyers can message sellers directly from a product page. Messages are stored in the `messages` table with sender_id, receiver_id, and optional product_id. The UI shows conversations in a chat-bubble format." |

---

## Pre-Presentation Checklist

- [ ] Website is hosted and accessible via URL
- [ ] Admin login works (admin@itradeza.co.za / Admin@123)
- [ ] At least 2-3 products are listed for demo
- [ ] Test user accounts exist (buyer + seller)
- [ ] Test a complete flow before presenting
- [ ] GitHub repo link ready to share
- [ ] Have the User Manual document open as backup

---

*Good luck with your presentation!*
