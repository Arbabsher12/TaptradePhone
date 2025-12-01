# Setup Files

This directory contains setup and migration scripts for the Buy & Sell Phone project.

## Setup Files Description

- `setup_brands_table.php` - Script to set up the brands table with initial data
- `setup_database.php` - Database initialization script
- `migrate_google_oauth.php` - Google OAuth migration script
- `add_dummy_phones.php` - Script to add realistic dummy phone data with images
- `run_dummy_data.php` - Web interface to run the dummy data script
- `setup_phone_images.php` - Creates placeholder phone images
- `update_dummy_data_with_phone_images.php` - Updates dummy data with phone images
- `download_phone_images_guide.php` - Guide for downloading real phone images

## Usage

These files are typically run once during initial project setup or when migrating data.

### Database Setup
1. Run `setup_database.php` to create the initial database structure
2. Run `setup_brands_table.php` to populate the brands table

### Google OAuth Migration
- Run `migrate_google_oauth.php` if you need to migrate existing user data to support Google OAuth

### Adding Dummy Data with Phone Images
- Run `setup_phone_images.php` to create placeholder phone images
- Run `add_dummy_phones.php` via command line: `php setup/add_dummy_phones.php`
- Run `update_dummy_data_with_phone_images.php` to update with phone images
- Or use the web interface: `http://your-domain/setup/run_dummy_data.php`
- For real phone images, visit: `http://your-domain/setup/download_phone_images_guide.php`

## Important Notes

- These scripts should be run with caution in production environments
- Always backup your database before running migration scripts
- These files may contain sensitive configuration data
- Remove or secure these files after successful setup
