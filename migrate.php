<?php
require_once __DIR__ . '/backend/database.php';

echo "Starting database migration...\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    echo "Creating properties table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS properties (
            id VARCHAR(50) PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            price DECIMAL(15,2) NOT NULL DEFAULT 0,
            location VARCHAR(255) NOT NULL,
            type ENUM('sale', 'rent') NOT NULL DEFAULT 'sale',
            bedrooms INT NOT NULL DEFAULT 0,
            bathrooms INT NOT NULL DEFAULT 0,
            area INT NOT NULL DEFAULT 0,
            image VARCHAR(500),
            featured TINYINT(1) NOT NULL DEFAULT 0,
            status VARCHAR(50) NOT NULL DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Creating sliders table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sliders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255),
            image VARCHAR(500),
            display_order INT NOT NULL DEFAULT 0,
            active TINYINT(1) NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Creating settings table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            setting_key VARCHAR(100) PRIMARY KEY,
            setting_value TEXT,
            setting_type VARCHAR(50) DEFAULT 'text',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Creating users table...\n";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "Checking if admin user exists...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $adminExists = $stmt->fetchColumn() > 0;

    if (!$adminExists) {
        echo "Creating default admin user (username: admin, password: admin123)...\n";
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')");
        $stmt->execute(['admin', $passwordHash]);
        echo "Admin user created successfully!\n";
    } else {
        echo "Admin user already exists, skipping...\n";
    }

    echo "Seeding default slider data...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sliders");
    $stmt->execute();
    $sliderCount = $stmt->fetchColumn();

    if ($sliderCount == 0) {
        $defaultSliders = [
            ['Find Your Dream Property', 'Discover the perfect place to call home', '', 1],
            ['Premium Real Estate Listings', 'Explore luxury homes and prime locations', '', 2],
            ['Your Trusted Property Partner', 'Professional service for buying and renting', '', 3]
        ];

        $stmt = $pdo->prepare("INSERT INTO sliders (title, subtitle, image, display_order) VALUES (?, ?, ?, ?)");
        foreach ($defaultSliders as $slider) {
            $stmt->execute($slider);
        }
        echo "Default slider data inserted!\n";
    } else {
        echo "Slider data already exists, skipping...\n";
    }

    echo "Seeding default settings...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    $settingsCount = $stmt->fetchColumn();

    if ($settingsCount == 0) {
        $defaultSettings = [
            ['site_name', 'Temer Properties', 'text'],
            ['site_tagline', 'Find Your Dream Property', 'text'],
            ['contact_email', 'info@temerproperties.com', 'email'],
            ['contact_phone', '+1234567890', 'text']
        ];

        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, setting_type) VALUES (?, ?, ?)");
        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
        echo "Default settings inserted!\n";
    } else {
        echo "Settings already exist, skipping...\n";
    }

    echo "\n✅ Migration completed successfully!\n";
    echo "\nDefault admin credentials:\n";
    echo "Username: admin\n";
    echo "Password: admin123\n";
    echo "\n⚠️  Please change the admin password after first login!\n";

} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
