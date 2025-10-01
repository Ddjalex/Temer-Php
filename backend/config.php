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
    return $db->fetchAll("SELECT * FROM sliders WHERE active = 1 ORDER BY display_order ASC");
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
