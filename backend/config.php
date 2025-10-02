<?php
require_once __DIR__ . '/database.php';

define('UPLOADS_DIR', __DIR__ . '/../frontend/assets/images/properties');

if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}

function getProperties($filters = []) {
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    
    if (!empty($filters['type'])) {
        $sql .= " AND type = ?";
        $params[] = $filters['type'];
    }
    
    if (isset($filters['minPrice']) && $filters['minPrice'] !== '') {
        $sql .= " AND price >= ?";
        $params[] = $filters['minPrice'];
    }
    
    if (isset($filters['maxPrice']) && $filters['maxPrice'] !== '') {
        $sql .= " AND price <= ?";
        $params[] = $filters['maxPrice'];
    }
    
    if (!empty($filters['location'])) {
        $sql .= " AND location LIKE ?";
        $params[] = '%' . $filters['location'] . '%';
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    return $db->fetchAll($sql, $params);
}

function getPropertyById($id) {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM properties WHERE id = ?", [$id]);
}

function createProperty($data) {
    $db = Database::getInstance();
    $id = generateId();
    
    $sql = "INSERT INTO properties (id, title, description, price, location, type, bedrooms, bathrooms, area, image, featured, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available', NOW())";
    
    $params = [
        $id,
        $data['title'],
        $data['description'] ?? '',
        $data['price'] ?? 0,
        $data['location'],
        $data['type'] ?? 'sale',
        $data['bedrooms'] ?? 0,
        $data['bathrooms'] ?? 0,
        $data['area'] ?? 0,
        $data['image'] ?? '',
        $data['featured'] ?? 0
    ];
    
    $db->query($sql, $params);
    return getPropertyById($id);
}

function updateProperty($id, $data) {
    $db = Database::getInstance();
    
    $fields = [];
    $params = [];
    
    if (isset($data['title'])) {
        $fields[] = "title = ?";
        $params[] = $data['title'];
    }
    if (isset($data['description'])) {
        $fields[] = "description = ?";
        $params[] = $data['description'];
    }
    if (isset($data['price'])) {
        $fields[] = "price = ?";
        $params[] = $data['price'];
    }
    if (isset($data['location'])) {
        $fields[] = "location = ?";
        $params[] = $data['location'];
    }
    if (isset($data['type'])) {
        $fields[] = "type = ?";
        $params[] = $data['type'];
    }
    if (isset($data['bedrooms'])) {
        $fields[] = "bedrooms = ?";
        $params[] = $data['bedrooms'];
    }
    if (isset($data['bathrooms'])) {
        $fields[] = "bathrooms = ?";
        $params[] = $data['bathrooms'];
    }
    if (isset($data['area'])) {
        $fields[] = "area = ?";
        $params[] = $data['area'];
    }
    if (isset($data['image'])) {
        $fields[] = "image = ?";
        $params[] = $data['image'];
    }
    if (isset($data['featured'])) {
        $fields[] = "featured = ?";
        $params[] = $data['featured'];
    }
    
    if (empty($fields)) {
        return getPropertyById($id);
    }
    
    $sql = "UPDATE properties SET " . implode(', ', $fields) . " WHERE id = ?";
    $params[] = $id;
    
    $db->query($sql, $params);
    return getPropertyById($id);
}

function deleteProperty($id) {
    $db = Database::getInstance();
    $db->query("DELETE FROM properties WHERE id = ?", [$id]);
    return true;
}

function generateId() {
    return uniqid('prop_');
}

function getSliders() {
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM sliders ORDER BY display_order ASC");
}

function getSliderById($id) {
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM sliders WHERE id = ?", [$id]);
}

function createSlider($data) {
    $db = Database::getInstance();
    
    $sql = "INSERT INTO sliders (title, subtitle, image, display_order, active, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $params = [
        $data['title'],
        $data['subtitle'] ?? '',
        $data['image'] ?? '',
        $data['display_order'] ?? 0,
        $data['active'] ?? 1
    ];
    
    $db->query($sql, $params);
    $id = $db->lastInsertId();
    return getSliderById($id);
}

function updateSlider($id, $data) {
    $db = Database::getInstance();
    
    $fields = [];
    $params = [];
    
    if (isset($data['title'])) {
        $fields[] = "title = ?";
        $params[] = $data['title'];
    }
    if (isset($data['subtitle'])) {
        $fields[] = "subtitle = ?";
        $params[] = $data['subtitle'];
    }
    if (isset($data['image'])) {
        $fields[] = "image = ?";
        $params[] = $data['image'];
    }
    if (isset($data['display_order'])) {
        $fields[] = "display_order = ?";
        $params[] = $data['display_order'];
    }
    if (isset($data['active'])) {
        $fields[] = "active = ?";
        $params[] = $data['active'];
    }
    
    if (empty($fields)) {
        return getSliderById($id);
    }
    
    $sql = "UPDATE sliders SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
    $params[] = $id;
    
    $db->query($sql, $params);
    return getSliderById($id);
}

function deleteSlider($id) {
    $db = Database::getInstance();
    $db->query("DELETE FROM sliders WHERE id = ?", [$id]);
    return true;
}

function handleImageUpload($file, $type = 'slider') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP allowed'];
    }
    
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File too large. Maximum 5MB allowed'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid($type . '_') . '.' . $extension;
    
    $uploadDir = __DIR__ . '/../frontend/assets/images/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'url' => '/frontend/assets/images/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}

function getSettings() {
    $db = Database::getInstance();
    $settings = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
    $result = [];
    foreach ($settings as $setting) {
        $result[$setting['setting_key']] = $setting['setting_value'];
    }
    return $result;
}
