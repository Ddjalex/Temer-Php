<?php
require_once __DIR__ . '/backend/database.php';

echo "Setting up images table in MariaDB...\n\n";

try {
    $db = Database::getInstance();
    
    $sql = "CREATE TABLE IF NOT EXISTS images (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        file_size INT NOT NULL,
        upload_date DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $db->query($sql);
    
    echo "✅ Images table created successfully!\n";
    echo "\nTable structure:\n";
    echo "- id: INT PRIMARY KEY AUTO_INCREMENT\n";
    echo "- title: VARCHAR(255) NOT NULL\n";
    echo "- file_path: VARCHAR(255) NOT NULL\n";
    echo "- file_size: INT NOT NULL\n";
    echo "- upload_date: DATETIME DEFAULT CURRENT_TIMESTAMP\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
