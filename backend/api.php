<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$method = $_SERVER['REQUEST_METHOD'];

if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
}

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

try {
    switch ($endpoint) {
        case 'properties':
            handleProperties($method, $id);
            break;
        case 'sliders':
            handleSliders($method, $id);
            break;
        case 'settings':
            handleSettings($method);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleProperties($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $property = getPropertyById($id);
                if ($property) {
                    echo json_encode($property);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Property not found']);
                }
            } else {
                $filters = [
                    'type' => $_GET['type'] ?? '',
                    'minPrice' => $_GET['minPrice'] ?? '',
                    'maxPrice' => $_GET['maxPrice'] ?? '',
                    'location' => $_GET['location'] ?? ''
                ];
                
                $properties = getProperties($filters);
                echo json_encode($properties);
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
                'title' => strip_tags($input['title']),
                'description' => strip_tags($input['description'] ?? ''),
                'price' => max(0, (float)($input['price'] ?? 0)),
                'location' => strip_tags($input['location']),
                'type' => in_array($input['type'] ?? 'sale', ['sale', 'rent']) ? $input['type'] : 'sale',
                'bedrooms' => max(0, (int)($input['bedrooms'] ?? 0)),
                'bathrooms' => max(0, (int)($input['bathrooms'] ?? 0)),
                'area' => max(0, (int)($input['area'] ?? 0)),
                'image' => filter_var($input['image'] ?? '', FILTER_SANITIZE_URL),
                'featured' => (bool)($input['featured'] ?? false)
            ];
            
            $created = createProperty($property);
            echo json_encode($created);
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
            
            $updates = [];
            if (isset($input['title'])) $updates['title'] = strip_tags($input['title']);
            if (isset($input['description'])) $updates['description'] = strip_tags($input['description']);
            if (isset($input['price'])) $updates['price'] = max(0, (float)$input['price']);
            if (isset($input['location'])) $updates['location'] = strip_tags($input['location']);
            if (isset($input['type']) && in_array($input['type'], ['sale', 'rent'])) $updates['type'] = $input['type'];
            if (isset($input['bedrooms'])) $updates['bedrooms'] = max(0, (int)$input['bedrooms']);
            if (isset($input['bathrooms'])) $updates['bathrooms'] = max(0, (int)$input['bathrooms']);
            if (isset($input['area'])) $updates['area'] = max(0, (int)$input['area']);
            if (isset($input['image'])) $updates['image'] = filter_var($input['image'], FILTER_SANITIZE_URL);
            if (isset($input['featured'])) $updates['featured'] = (bool)$input['featured'];
            
            $updated = updateProperty($id, $updates);
            if ($updated) {
                echo json_encode($updated);
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
            
            $deleted = deleteProperty($id);
            if ($deleted) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Property not found']);
            }
            break;
    }
}

function handleSliders($method, $id) {
    switch ($method) {
        case 'GET':
            if ($id) {
                $slider = getSliderById($id);
                if ($slider) {
                    echo json_encode($slider);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Slider not found']);
                }
            } else {
                $sliders = getSliders();
                echo json_encode($sliders);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid JSON']);
                return;
            }
            
            if (empty($input['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Title is required']);
                return;
            }
            
            $slider = [
                'title' => strip_tags($input['title']),
                'subtitle' => strip_tags($input['subtitle'] ?? ''),
                'image' => filter_var($input['image'] ?? '', FILTER_SANITIZE_URL),
                'display_order' => max(0, (int)($input['display_order'] ?? 0)),
                'active' => isset($input['active']) ? (int)(bool)$input['active'] : 1
            ];
            
            $created = createSlider($slider);
            echo json_encode($created);
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
            
            $updates = [];
            if (isset($input['title'])) $updates['title'] = strip_tags($input['title']);
            if (isset($input['subtitle'])) $updates['subtitle'] = strip_tags($input['subtitle']);
            if (isset($input['image'])) $updates['image'] = filter_var($input['image'], FILTER_SANITIZE_URL);
            if (isset($input['display_order'])) $updates['display_order'] = max(0, (int)$input['display_order']);
            if (isset($input['active'])) $updates['active'] = (int)(bool)$input['active'];
            
            $updated = updateSlider($id, $updates);
            if ($updated) {
                echo json_encode($updated);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Slider not found']);
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'ID required']);
                return;
            }
            
            $deleted = deleteSlider($id);
            if ($deleted) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Slider not found']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}

function handleSettings($method) {
    switch ($method) {
        case 'GET':
            $settings = getSettings();
            echo json_encode($settings);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
}
