# Buy & Sell Phone - E-Marketplace

A web-based marketplace for buying and selling phones built with PHP, MySQL, and modern web technologies.

## Project Structure

```
buy_sell_Phone/
├── Components/          # Static assets (images, icons)
├── css/                # Stylesheets
├── html/               # HTML/PHP view files
├── js/                 # JavaScript files
├── php/                # PHP backend logic and API endpoints
├── setup/              # Setup and migration scripts
├── tests/              # Test and debugging files
├── uploads/            # User uploaded files
├── index.php           # Main entry point
├── db.php              # Database configuration
└── README.md           # This file
```

## Features

- User registration and authentication
- Phone listing and management
- Search and filtering
- Messaging system
- Profile management
- Google OAuth integration
- Email verification with OTP
- Password reset functionality

## Setup Instructions

1. **Database Setup**: Run the setup scripts in the `setup/` directory
2. **Configuration**: Update database credentials in `db.php`
3. **Dependencies**: Ensure PHP 7.4+ and MySQL are installed
4. **Web Server**: Configure Apache/Nginx or use PHP built-in server

## Development

- Test files are located in the `tests/` directory
- Setup scripts are in the `setup/` directory
- Main application code is organized in `html/`, `php/`, `css/`, and `js/` directories

## Documentation

- `DEPLOYMENT_GUIDE.md` - Deployment instructions
- `BREVO_SETUP_GUIDE.md` - Email service setup
- `GOOGLE_OAUTH_SETUP.md` - Google OAuth configuration
- `RUNNING_WITHOUT_XAMPP.md` - Alternative server setup
