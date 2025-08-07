<?php
/**
 * Fallback AJAX routes for profile completion
 * This file provides direct database access when Laravel routes fail
 */

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Database configuration (matching Laravel's .env)
    $host = 'localhost';
    $dbname = 'lms';
    $username = 'root';
    $password = '';
    
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? ($_GET['country_id'] ?? ($_GET['state_id'] ?? ''));
    $search = $_GET['search'] ?? '';
    
    switch ($action) {
        case 'get-states':
        case 'get_states':
            if (empty($id)) {
                echo json_encode(['results' => []]);
                exit;
            }
            
            $sql = "SELECT id, name FROM states WHERE country_id = :country_id";
            $params = ['country_id' => $id];
            
            if (!empty($search)) {
                $sql .= " AND name LIKE :search";
                $params['search'] = "%$search%";
            }
            
            $sql .= " ORDER BY name ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $states = $stmt->fetchAll();
            
            $response = [];
            foreach ($states as $state) {
                $response[] = [
                    'id' => $state['id'],
                    'text' => $state['name'],
                    'name' => $state['name'] // For backward compatibility
                ];
            }
            
            echo json_encode(['results' => $response]);
            break;
            
        case 'get-cities':
        case 'get_cities':
            if (empty($id)) {
                echo json_encode(['results' => []]);
                exit;
            }
            
            $sql = "SELECT id, name FROM spn_cities WHERE state_id = :state_id";
            $params = ['state_id' => $id];
            
            if (!empty($search)) {
                $sql .= " AND name LIKE :search";
                $params['search'] = "%$search%";
            }
            
            $sql .= " ORDER BY name ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $cities = $stmt->fetchAll();
            
            $response = [];
            foreach ($cities as $city) {
                $response[] = [
                    'id' => $city['id'],
                    'text' => $city['name'],
                    'name' => $city['name'] // For backward compatibility
                ];
            }
            
            echo json_encode(['results' => $response]);
            break;
            
        case 'test':
            // Test endpoint to verify the script is working
            echo json_encode([
                'status' => 'success',
                'message' => 'Fallback AJAX routes are working',
                'timestamp' => date('Y-m-d H:i:s'),
                'received_params' => $_GET
            ]);
            break;
            
        default:
            echo json_encode([
                'error' => 'Invalid action',
                'available_actions' => ['get-states', 'get-cities', 'test'],
                'received_action' => $action
            ]);
            break;
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database connection failed',
        'message' => 'Unable to connect to database',
        'debug' => $e->getMessage() // Remove this in production
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => 'An unexpected error occurred',
        'debug' => $e->getMessage() // Remove this in production
    ]);
}
?>