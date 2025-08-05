<?php
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = $_GET['query'] ?? 'countries';
    
    switch ($query) {
        case 'countries':
            $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name LIMIT 10");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'states':
            $countryId = $_GET['country_id'] ?? 38;
            $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? ORDER BY name LIMIT 10");
            $stmt->execute([$countryId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'cities':
            $stateId = $_GET['state_id'] ?? 659;
            $stmt = $pdo->prepare("SELECT id, name FROM spn_cities WHERE state_id = ? ORDER BY name LIMIT 10");
            $stmt->execute([$stateId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        default:
            $results = [];
    }
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'count' => count($results),
        'data' => $results
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'query' => $query ?? 'unknown'
    ]);
}
?> 