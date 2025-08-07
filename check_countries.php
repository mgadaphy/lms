<?php
// Simple script to check country data
try {
    $pdo = new PDO("mysql:host=localhost;dbname=lms;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check country ID 38
    $stmt = $pdo->prepare("SELECT id, name FROM countries WHERE id = 38");
    $stmt->execute();
    $country38 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Country ID 38: " . json_encode($country38) . "\n";
    
    // Check country ID 39
    $stmt = $pdo->prepare("SELECT id, name FROM countries WHERE id = 39");
    $stmt->execute();
    $country39 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Country ID 39: " . json_encode($country39) . "\n";
    
    // Check if there are states for country 38
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM states WHERE country_id = 38");
    $stmt->execute();
    $stateCount = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "States for country 38: " . $stateCount['count'] . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>