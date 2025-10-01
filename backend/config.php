<?php
define('DATA_DIR', __DIR__ . '/../data');
define('UPLOADS_DIR', __DIR__ . '/../frontend/assets/images/properties');
define('PROPERTIES_FILE', DATA_DIR . '/properties.json');

if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

if (!file_exists(UPLOADS_DIR)) {
    mkdir(UPLOADS_DIR, 0777, true);
}

if (!file_exists(PROPERTIES_FILE)) {
    file_put_contents(PROPERTIES_FILE, json_encode([], JSON_PRETTY_PRINT));
}

function getProperties() {
    $data = file_get_contents(PROPERTIES_FILE);
    return json_decode($data, true) ?: [];
}

function saveProperties($properties) {
    return file_put_contents(PROPERTIES_FILE, json_encode($properties, JSON_PRETTY_PRINT));
}

function generateId() {
    return uniqid('prop_');
}
