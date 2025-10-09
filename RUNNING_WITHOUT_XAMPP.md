# Buy & Sell Phone - Running Without XAMPP

This guide will help you run your PHP phone marketplace application using the built-in PHP development server instead of XAMPP.

## Prerequisites

1. **PHP 7.4+** installed on your system
2. **MySQL** server running (or use SQLite alternative)

## Quick Start

### Option 1: Using MySQL (Recommended)

1. **Start MySQL server** (if not already running)
   - Windows: Start MySQL service or use XAMPP's MySQL only
   - Linux/Mac: `sudo service mysql start` or `brew services start mysql`

2. **Set up the database**:
   ```bash
   php setup_database.php
   ```

3. **Start the PHP server**:
   ```bash
   php -S localhost:8000
   ```

4. **Open your browser** and visit:
   ```
   http://localhost:8000
   ```

### Option 2: Using SQLite (No MySQL required)

1. **Modify database configuration**:
   - Open `db.php`
   - Comment out the MySQL connection code
   - Uncomment and use the SQLite code from `db_sqlite.php`

2. **Start the PHP server**:
   ```bash
   php -S localhost:8000
   ```

3. **Open your browser** and visit:
   ```
   http://localhost:8000
   ```

## Project Structure

```
buy_sell_Phone/
├── index.php              # Main entry point (redirects to html/home.php)
├── db.php                 # Database configuration
├── setup_database.php     # Database setup script
├── db_sqlite.php          # SQLite alternative
├── html/                  # Main application pages
│   ├── home.php          # Home page
│   ├── login.php         # Login page
│   ├── signup.php        # Registration page
│   └── ...
├── php/                  # Backend PHP scripts
├── css/                  # Stylesheets
├── js/                   # JavaScript files
└── uploads/              # File uploads
```

## Features

- **Clean URLs**: Modern URL structure without .php extensions
- **User Authentication**: Login/Register system
- **Phone Listings**: Browse and search phones
- **Sell Phones**: Create new listings
- **Messaging**: Chat with sellers
- **User Profiles**: Manage account settings
- **RESTful API**: Clean API endpoints for AJAX requests

## Clean URLs

The application now uses clean URLs instead of traditional PHP file paths:

### Page URLs
- `/` - Home page
- `/login` - Login page
- `/signup` - Registration page
- `/profile` - User profile
- `/mylisting` - My listings
- `/sellYourPhone` - Sell phone page
- `/phoneDetail` - Phone details
- `/conversations` - Messages
- `/chats` - Chat interface

### API Endpoints
- `/api/phones` - Get phone listings
- `/api/models` - Get phone models by brand
- `/api/messages` - Get/send messages

### Form Actions
- `/login` - Process login
- `/signup` - Process registration
- `/sellPhone` - Process phone sale
- `/updateProfile` - Update user profile
- `/updatePhone` - Update phone listing
- `/deletePhone` - Delete phone listing

## Troubleshooting

### Database Connection Issues

1. **MySQL not running**:
   - Start MySQL service
   - Check if port 3306 is available
   - Verify username/password in `db.php`

2. **Database doesn't exist**:
   - Run `php setup_database.php`
   - Or manually create database `buy_sell_phone`

3. **Permission errors**:
   - Check file permissions on `uploads/` directory
   - Ensure PHP has write access

### Server Issues

1. **Port already in use**:
   ```bash
   php -S localhost:8001  # Use different port
   ```

2. **PHP not found**:
   - Add PHP to your system PATH
   - Or use full path: `C:\php\php.exe -S localhost:8000`

### Alternative Database Setup

If you don't want to install MySQL:

1. **Use SQLite** (see Option 2 above)
2. **Use portable MySQL**:
   - Download portable MySQL
   - Extract and run `mysqld.exe`
   - Update `db.php` with correct credentials

## Development Tips

- The built-in PHP server is for development only
- For production, use Apache/Nginx with PHP-FPM
- Always backup your database before making changes
- Use version control (Git) for your code

## Default Credentials

The database comes with sample data. You can:
- Register new accounts
- Use existing test accounts from the database dump
- Reset the database by running `setup_database.php` again

---

**Happy coding!** 🚀
