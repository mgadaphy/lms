<?php
// Standalone AJAX handler for cities - bypasses broken Laravel
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get parameters
    $stateId = $_GET['id'] ?? null;
    $search = $_GET['search'] ?? '';
    $page = $_GET['page'] ?? 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    
    if (!$stateId) {
        echo json_encode(['results' => [], 'pagination' => ['more' => false]]);
        exit;
    }
    
    // Build query
    $whereConditions = ['state_id = ?'];
    $params = [$stateId];
    
    if (!empty($search)) {
        $whereConditions[] = 'name LIKE ?';
        $params[] = '%' . $search . '%';
    }
    
    $whereClause = implode(' AND ', $whereConditions);
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM spn_cities WHERE $whereClause";
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $total = $stmt->fetch()['total'];
    
    // Get cities
    $query = "SELECT id, name FROM spn_cities WHERE $whereClause ORDER BY name LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response for Select2
    $results = [];
    foreach ($cities as $city) {
        $results[] = [
            'id' => $city['id'],
            'text' => $city['name']
        ];
    }
    
    // Determine if there are more results
    $hasMore = ($offset + $limit) < $total;
    
    echo json_encode([
        'results' => $results,
        'pagination' => ['more' => $hasMore]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'results' => [],
        'pagination' => ['more' => false]
    ]);
}
?> 