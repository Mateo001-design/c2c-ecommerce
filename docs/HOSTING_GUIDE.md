# Hosting Guide — Deploy iTradeZA on InfinityFree

This step-by-step guide will help you deploy iTradeZA on a free hosting provider (InfinityFree) so you can present a live demo.

---

## Option A: InfinityFree (Recommended — Free)

### Step 1: Create an Account
1. Go to https://www.infinityfree.com/
2. Click "Sign Up" and create an account
3. Verify your email

### Step 2: Create a Hosting Account
1. From the dashboard, click **"Create Account"**
2. Choose a subdomain (e.g., `itradeza.infinityfreeapp.com`) or connect your own domain
3. Set a label like "iTradeZA"
4. Click **"Create"**

### Step 3: Create the MySQL Database
1. In your hosting dashboard, go to **"MySQL Databases"**
2. Click **"Create Database"**
3. Note down:
   - Database name (e.g., `if0_12345678_itradeza`)
   - Database host (e.g., `sql123.infinityfree.com`)
   - Username (auto-generated)
   - Password (you set this)

### Step 4: Import the Database Schema
1. Go to **phpMyAdmin** from your hosting dashboard
2. Select your database
3. Click the **"Import"** tab
4. Upload `sql/database.sql`
5. Click **"Go"** to import

### Step 5: Update Configuration
Edit `config/database.php` with your hosting details:
```php
define('DB_HOST', 'sql123.infinityfree.com');  // Your DB host
define('DB_NAME', 'if0_12345678_itradeza');    // Your DB name
define('DB_USER', 'if0_12345678');             // Your DB username
define('DB_PASS', 'your_password_here');       // Your DB password

define('SITE_URL', 'https://itradeza.infinityfreeapp.com'); // Your site URL
```

### Step 6: Upload Files via FTP
1. Download an FTP client: [FileZilla](https://filezilla-project.org/)
2. Connect with credentials from your hosting dashboard:
   - Host: `ftpupload.net`
   - Username: (from dashboard)
   - Password: (from dashboard)
   - Port: `21`
3. Navigate to the `htdocs/` folder on the server
4. Upload ALL project files (everything except `.git/` folder and `docs/`)
5. Make sure `uploads/products/` directory exists on the server

### Step 7: Set File Permissions
In the hosting File Manager or via FTP:
- Set `uploads/` folder to permissions `755`
- All `.php` files should be `644`

### Step 8: Verify
1. Visit your site URL (e.g., `https://itradeza.infinityfreeapp.com`)
2. Login with admin: `admin@itradeza.co.za` / `Admin@123`
3. Test registration, product listing, and cart

---

## Option B: 000webhost (Alternative — Free)

1. Go to https://www.000webhost.com/
2. Sign up and create a website
3. Use the File Manager to upload files
4. Create a database in the dashboard
5. Import `sql/database.sql`
6. Update `config/database.php`

---

## Option C: Railway.app (Modern — Free Tier)

1. Go to https://railway.app/
2. Connect your GitHub repository
3. Add a MySQL service
4. Set environment variables for DB credentials
5. Deploy automatically from your repo

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Database connection failed" | Double-check DB credentials in `config/database.php` |
| Images not uploading | Check `uploads/` folder permissions (755) |
| 500 Internal Server Error | Check PHP version (need 7.4+) in hosting settings |
| CSS not loading | Verify `SITE_URL` matches your actual domain |
| "Access denied" for admin | Re-run the admin password hash in phpMyAdmin |

### Re-setting Admin Password
If admin login doesn't work, run this SQL in phpMyAdmin:
```sql
UPDATE users SET password = '$2y$10$8K1p.kG1e2V8Hh3Q5D7P7uX3N7VF8mC4E6Y9R2A0B5J1K3L4M6N8O' 
WHERE email = 'admin@itradeza.co.za';
```
Then use password: `Admin@123`

---

## Important Notes for Submission

1. **Submit the LIVE URL** to your lecturer (not localhost)
2. **Submit the GitHub repo link**: https://github.com/Mateo001-design/c2c-ecommerce
3. Do this **ONE WEEK before** your presentation date
4. Test all features on the live site before presenting

---

*Good luck with your presentation!*
