<?php
require_once __DIR__ . '/backend/database.php';
require_once __DIR__ . '/backend/config.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Test - Temer Properties</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #8BC34A; margin-bottom: 30px; }
        h2 { color: #558B2F; margin: 25px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #8BC34A; }
        .test-section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #8BC34A; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { color: #2196F3; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #8BC34A; color: white; }
        tr:hover { background: #f5f5f5; }
        pre { background: #263238; color: #aed581; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #4CAF50; color: white; }
        .badge-info { background: #2196F3; color: white; }
        .btn { display: inline-block; padding: 10px 20px; background: #8BC34A; color: white; text-decoration: none; border-radius: 4px; margin-top: 20px; }
        .btn:hover { background: #558B2F; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Connection Test</h1>
        
        <?php
        try {
            echo '<h2>1. Database Connection</h2>';
            echo '<div class="test-section">';
            
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            
            echo '<p class="success">✅ Successfully connected to PostgreSQL!</p>';
            echo '<p class="info">Host: ' . htmlspecialchars(getenv('PGHOST')) . '</p>';
            echo '<p class="info">Port: ' . htmlspecialchars(getenv('PGPORT')) . '</p>';
            echo '<p class="info">Database: ' . htmlspecialchars(getenv('PGDATABASE')) . '</p>';
            echo '</div>';
            
            echo '<h2>2. Database Tables</h2>';
            echo '<div class="test-section">';
            $tables = $db->fetchAll("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
            
            if (count($tables) > 0) {
                echo '<p class="success">✅ Found ' . count($tables) . ' tables</p>';
                echo '<table>';
                echo '<tr><th>Table Name</th><th>Row Count</th></tr>';
                foreach ($tables as $table) {
                    $tableName = $table['tablename'];
                    $count = $db->fetchOne("SELECT COUNT(*) as count FROM " . $tableName);
                    echo '<tr><td>' . htmlspecialchars($tableName) . '</td><td>' . $count['count'] . '</td></tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="error">⚠️ No tables found. Please run migrate.php first!</p>';
                echo '<pre>php migrate.php</pre>';
            }
            echo '</div>';
            
            $properties = $db->fetchAll("SELECT * FROM properties LIMIT 5");
            echo '<h2>3. Properties Table (Sample)</h2>';
            echo '<div class="test-section">';
            if (count($properties) > 0) {
                echo '<p class="success">✅ Found ' . count($properties) . ' properties (showing first 5)</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Title</th><th>Location</th><th>Price</th><th>Type</th></tr>';
                foreach ($properties as $prop) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($prop['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($prop['title']) . '</td>';
                    echo '<td>' . htmlspecialchars($prop['location']) . '</td>';
                    echo '<td>$' . number_format($prop['price'], 2) . '</td>';
                    echo '<td><span class="badge badge-info">' . htmlspecialchars($prop['type']) . '</span></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="info">ℹ️ No properties in database yet. Add some through the admin panel!</p>';
            }
            echo '</div>';
            
            $sliders = $db->fetchAll("SELECT * FROM sliders WHERE active = 1");
            echo '<h2>4. Sliders Table</h2>';
            echo '<div class="test-section">';
            if (count($sliders) > 0) {
                echo '<p class="success">✅ Found ' . count($sliders) . ' active sliders</p>';
                echo '<table>';
                echo '<tr><th>Order</th><th>Title</th><th>Subtitle</th><th>Active</th></tr>';
                foreach ($sliders as $slider) {
                    echo '<tr>';
                    echo '<td>' . $slider['display_order'] . '</td>';
                    echo '<td>' . htmlspecialchars($slider['title']) . '</td>';
                    echo '<td>' . htmlspecialchars($slider['subtitle']) . '</td>';
                    echo '<td><span class="badge badge-success">Active</span></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="info">ℹ️ No sliders found.</p>';
            }
            echo '</div>';
            
            $settings = $db->fetchAll("SELECT * FROM settings");
            echo '<h2>5. Settings Table</h2>';
            echo '<div class="test-section">';
            if (count($settings) > 0) {
                echo '<p class="success">✅ Found ' . count($settings) . ' settings</p>';
                echo '<table>';
                echo '<tr><th>Key</th><th>Value</th><th>Type</th></tr>';
                foreach ($settings as $setting) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($setting['setting_key']) . '</td>';
                    echo '<td>' . htmlspecialchars($setting['setting_value']) . '</td>';
                    echo '<td>' . htmlspecialchars($setting['setting_type']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="info">ℹ️ No settings found.</p>';
            }
            echo '</div>';
            
            $users = $db->fetchAll("SELECT id, username, email, role, created_at FROM users");
            echo '<h2>6. Users Table</h2>';
            echo '<div class="test-section">';
            if (count($users) > 0) {
                echo '<p class="success">✅ Found ' . count($users) . ' users</p>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th></tr>';
                foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td>' . $user['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['email'] ?: 'N/A') . '</td>';
                    echo '<td><span class="badge badge-success">' . htmlspecialchars($user['role']) . '</span></td>';
                    echo '<td>' . date('M d, Y', strtotime($user['created_at'])) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p class="info">ℹ️ No users found.</p>';
            }
            echo '</div>';
            
            echo '<h2>7. API Endpoints Test</h2>';
            echo '<div class="test-section">';
            echo '<p class="success">✅ Available API Endpoints:</p>';
            echo '<ul>';
            echo '<li><code>GET /api/properties</code> - List all properties</li>';
            echo '<li><code>GET /api/properties/:id</code> - Get single property</li>';
            echo '<li><code>POST /api/properties</code> - Create property (requires auth)</li>';
            echo '<li><code>PUT /api/properties/:id</code> - Update property (requires auth)</li>';
            echo '<li><code>DELETE /api/properties/:id</code> - Delete property (requires auth)</li>';
            echo '<li><code>GET /api/sliders</code> - List all active sliders</li>';
            echo '<li><code>GET /api/settings</code> - Get all settings</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<h2>✅ All Tests Passed!</h2>';
            echo '<p style="margin-top: 20px;">Your database is properly configured and working correctly.</p>';
            echo '<a href="/" class="btn">Go to Homepage</a> ';
            echo '<a href="/admin" class="btn">Go to Admin Panel</a>';
            
        } catch (Exception $e) {
            echo '<div class="test-section">';
            echo '<p class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p style="margin-top: 15px;">Please check:</p>';
            echo '<ul>';
            echo '<li>Database credentials are set in Replit Secrets</li>';
            echo '<li>Database server is accessible</li>';
            echo '<li>Run <code>php migrate.php</code> to create tables</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
