# Setup Files

This directory contains setup and migration scripts for the Buy & Sell Phone project.

## Setup Files Description

- `setup_brands_table.php` - Script to set up the brands table with initial data
- `setup_database.php` - Database initialization script
- `migrate_google_oauth.php` - Google OAuth migration script

## Usage

These files are typically run once during initial project setup or when migrating data.

### Database Setup
1. Run `setup_database.php` to create the initial database structure
2. Run `setup_brands_table.php` to populate the brands table

### Google OAuth Migration
- Run `migrate_google_oauth.php` if you need to migrate existing user data to support Google OAuth

## Important Notes

- These scripts should be run with caution in production environments
- Always backup your database before running migration scripts
- These files may contain sensitive configuration data
- Remove or secure these files after successful setup
