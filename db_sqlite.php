<?php
/**
 * SQLite Database Configuration (Alternative to MySQL)
 * Use this if you don't want to install MySQL separately
 */

// Uncomment the lines below and comment out the MySQL configuration in db.php
// if you want to use SQLite instead

/*
$dbPath = __DIR__ . '/database/buy_sell_phone.db';

// Create database directory if it doesn't exist
if (!file_exists(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0755, true);
}

try {
    $conn = new PDO("sqlite:$dbPath");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables (simplified version)
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            phone TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            profile_picture TEXT DEFAULT '/Components/noDp.png'
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS brands (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            logo TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS phone_models (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            brand_id INTEGER NOT NULL,
            model_name TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (brand_id) REFERENCES brands (id)
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS phones (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            phone_name TEXT NOT NULL,
            phone_price DECIMAL(10,2) NOT NULL,
            phone_condition INTEGER NOT NULL,
            phone_details TEXT,
            image_paths TEXT DEFAULT 'uploads/none.jpg',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            views INTEGER DEFAULT 0,
            brand_id INTEGER,
            model_id INTEGER,
            phone_storage TEXT,
            phone_color TEXT,
            sellerId INTEGER NOT NULL,
            seller_name TEXT NOT NULL,
            seller_email TEXT NOT NULL DEFAULT '1',
            seller_phone TEXT NOT NULL,
            seller_location TEXT NOT NULL,
            FOREIGN KEY (brand_id) REFERENCES brands (id),
            FOREIGN KEY (model_id) REFERENCES phone_models (id)
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS conversations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user1_id INTEGER NOT NULL,
            user2_id INTEGER NOT NULL,
            phone_id INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_message_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user1_id) REFERENCES users (user_id),
            FOREIGN KEY (user2_id) REFERENCES users (user_id),
            FOREIGN KEY (phone_id) REFERENCES phones (id)
        )
    ");
    
    $conn->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            conversation_id INTEGER NOT NULL,
            sender_id INTEGER NOT NULL,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (conversation_id) REFERENCES conversations (id),
            FOREIGN KEY (sender_id) REFERENCES users (user_id)
        )
    ");
    
    // Insert sample data
    $conn->exec("
        INSERT OR IGNORE INTO brands (id, name) VALUES 
        (1, 'Apple'), (2, 'Samsung'), (3, 'Google'), (4, 'Xiaomi'), (5, 'OnePlus')
    ");
    
    $conn->exec("
        INSERT OR IGNORE INTO phone_models (id, brand_id, model_name) VALUES 
        (1, 1, 'iPhone 15 Pro Max'), (2, 1, 'iPhone 15 Pro'), (3, 1, 'iPhone 15'),
        (4, 2, 'Galaxy S23 Ultra'), (5, 2, 'Galaxy S23+'), (6, 2, 'Galaxy S23')
    ");
    
    echo "SQLite database created successfully!";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
*/
?>
