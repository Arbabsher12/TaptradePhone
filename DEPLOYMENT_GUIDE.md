# Deployment Guide for Buy & Sell Phone Application

## Quick Fix for 404 Errors

The 404 errors you're experiencing are likely due to server configuration issues. Here's how to fix them:

### 1. Update Database Configuration

Edit the `db.php` file and replace the hosting database credentials:

```php
// In db.php, update these lines:
$username = "your_actual_hosting_db_username";
$password = "your_actual_hosting_db_password"; 
$dbname = "your_actual_hosting_db_name";
```

### 2. Upload Database

You need to create the database on your hosting server:

1. **Option A: Use phpMyAdmin (if available)**
   - Log into your hosting control panel
   - Open phpMyAdmin
   - Create a new database with the name you specified in `db.php`
   - Import the `buy_sell_phone.sql` file

2. **Option B: Use the setup script**
   - Upload the `setup_database.php` file
   - Visit `http://yourdomain.com/setup_database.php` in your browser
   - Follow the instructions

### 3. File Permissions

Make sure these directories have write permissions (755 or 777):
- `uploads/` directory
- `php/uploads/` directory

### 4. Test the Setup

1. Visit `http://yourdomain.com/test_server.php` to check server configuration
2. Visit `http://yourdomain.com/` to test the main application

### 5. Common Issues and Solutions

#### Issue: 404 File Not Found
**Solution:** The `.htaccess` file has been updated to work with most hosting providers. If you still get 404 errors:

1. **Test server compatibility**: Visit `http://yourdomain.com/server_test.php`
2. **Check if your hosting supports `.htaccess` files**
3. **Make sure `mod_rewrite` is enabled**
4. **Try accessing `http://yourdomain.com/index.php` directly**
5. **If .htaccess doesn't work, use the fallback version**:
   - Rename `index.php` to `index_advanced.php`
   - Rename `index_fallback.php` to `index.php`
   - This provides simple routing without URL rewriting

#### Issue: Database Connection Failed
**Solution:** 
1. Verify database credentials in `db.php`
2. Make sure the database exists on your hosting server
3. Check if your hosting allows external database connections

#### Issue: Images Not Loading
**Solution:**
1. Check file permissions on the `uploads/` directory
2. Verify the `uploads/` directory exists
3. Make sure image files were uploaded correctly

### 6. File Structure Verification

Make sure your hosting directory contains all these files:
```
/
├── index.php (contains all routing logic)
├── db.php
├── .htaccess
├── buy_sell_phone.sql
├── setup_database.php
├── debug.php
├── minimal_home.php
├── html/
│   ├── home.php
│   ├── login.php
│   ├── signup.php
│   ├── sellYourPhone.php
│   └── ...
├── php/
│   ├── sellPhone.php
│   ├── signin.php
│   ├── register.php
│   └── ...
├── css/
├── js/
├── uploads/
└── Components/
```

**Note:** The router logic has been merged into `index.php` to avoid include issues on hosting servers.

### 7. Testing Checklist

- [ ] Database connection works
- [ ] Home page loads
- [ ] Login page loads
- [ ] Sell phone page loads
- [ ] Images upload correctly
- [ ] User registration works
- [ ] User login works

### 8. Support

If you continue to have issues:

1. Check your hosting provider's documentation
2. Contact your hosting support
3. Verify all files were uploaded correctly via FileZilla
4. Check error logs in your hosting control panel

## Quick Commands for FileZilla

When uploading via FileZilla:
1. Upload all files to the `public_html` or `www` directory
2. Set permissions: Right-click on folders → File Permissions → 755
3. Set permissions for `uploads/` folder: 777 (if needed)
4. Make sure `.htaccess` file is uploaded (it might be hidden)

## Alternative: Manual Database Setup

If automatic setup doesn't work:

1. Create database via hosting control panel
2. Create tables manually using the SQL from `buy_sell_phone.sql`
3. Insert sample data from the SQL file
4. Test the application
