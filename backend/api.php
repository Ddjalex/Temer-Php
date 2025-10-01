<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (count($pathParts) < 2 || $pathParts[0] !== 'api') {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit();
}

$endpoint = $pathParts[1];
$id = $pathParts[2] ?? null;

switch ($endpoint) {
    case 'properties':
        handleProperties($method, $id);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
}

function handleProperties($method, $id) {
    $properties = getProperties();
    
    switch ($method) {
        case 'GET':
            if ($id) {
                $property = array_values(array_filter($properties, fn($p) => $p['id'] === $id))[0] ?? null;
                if ($property) {
                    echo json_encode($property);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Property not found']);
                }
            } else {
                $type = $_GET['type'] ?? '';
                $minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : null;
                $maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : null;
                $location = $_GET['location'] ?? '';
                
                $filtered = array_filter($properties, function($p) use ($type, $minPrice, $maxPrice, $location) {
                    if ($type && $p['type'] !== $type) return false;
                    if ($minPrice && $p['price'] < $minPrice) return false;
                    if ($maxPrice && $p['price'] > $maxPrice) return false;
                    if ($location && stripos($p['location'], $location) === false) return false;
                    return true;
                });
                
                echo json_encode(array_values($filtered));
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }
            
            if (empty($input['title']) || empty($input['location'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Title and location are required']);
                return;
            }
            
            $property = [
                'id' => generateId(),
                'title' => strip_tags($input['title']),
                'description' => strip_tags($input['description'] ?? ''),
                'price' => max(0, (float)($input['price'] ?? 0)),
                'location' => strip_tags($input['location']),
                'type' => in_array($input['type'] ?? 'sale', ['sale', 'rent']) ? $input['type'] : 'sale',
                'bedrooms' => max(0, (int)($input['bedrooms'] ?? 0)),
                'bathrooms' => max(0, (int)($input['bathrooms'] ?? 0)),
                'area' => max(0, (int)($input['area'] ?? 0)),
                'image' => filter_var($input['image'] ?? '', FILTER_SANITIZE_URL),
                'featured' => (bool)($input['featured'] ?? false),
                'status' => 'available',
                'createdAt' => date('Y-m-d H:i:s')
            ];
            
            $properties[] = $property;
            saveProperties($properties);
            
            echo json_encode($property);
            break;
            
        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }
            
            $found = false;
            
            foreach ($properties as &$p) {
                if ($p['id'] === $id) {
                    if (isset($input['title'])) $p['title'] = strip_tags($input['title']);
                    if (isset($input['description'])) $p['description'] = strip_tags($input['description']);
                    if (isset($input['price'])) $p['price'] = max(0, (float)$input['price']);
                    if (isset($input['location'])) $p['location'] = strip_tags($input['location']);
                    if (isset($input['type']) && in_array($input['type'], ['sale', 'rent'])) $p['type'] = $input['type'];
                    if (isset($input['bedrooms'])) $p['bedrooms'] = max(0, (int)$input['bedrooms']);
                    if (isset($input['bathrooms'])) $p['bathrooms'] = max(0, (int)$input['bathrooms']);
                    if (isset($input['area'])) $p['area'] = max(0, (int)$input['area']);
                    if (isset($input['image'])) $p['image'] = filter_var($input['image'], FILTER_SANITIZE_URL);
                    if (isset($input['featured'])) $p['featured'] = (bool)$input['featured'];
                    
                    $found = true;
                    echo json_encode($p);
                    break;
                }
            }
            
            if ($found) {
                saveProperties($properties);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Property not found']);
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                return;
            }
            
            $newProperties = array_values(array_filter($properties, fn($p) => $p['id'] !== $id));
            
            if (count($newProperties) < count($properties)) {
                saveProperties($newProperties);
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Property not found']);
            }
            break;
    }
}
