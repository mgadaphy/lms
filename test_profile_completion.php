<?php

// Direct database test using actual credentials
try {
    echo "=== Profile Completion Database Test ===\n\n";
    
    // Database credentials from .env
    $host = 'localhost';
    $database = 'lms';
    $username = 'root';
    $password = '';
    
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connected successfully\n\n";
    
    // Check table existence and counts
    echo "1. Checking table data...\n";
    
    $tables = ['countries', 'states', 'spn_cities'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "{$table}: {$result['count']} records\n";
        } catch (Exception $e) {
            echo "{$table}: ERROR - {$e->getMessage()}\n";
        }
    }
    
    echo "\n2. Testing country-state relationships...\n";
    
    // Get a sample country
    $stmt = $pdo->query("SELECT id, name FROM countries LIMIT 1");
    $country = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($country) {
        echo "Sample country: {$country['name']} (ID: {$country['id']})\n";
        
        // Get states for this country
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM states WHERE country_id = ?");
        $stmt->execute([$country['id']]);
        $stateCount = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "States for {$country['name']}: {$stateCount['count']}\n";
        
        if ($stateCount['count'] > 0) {
            $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? LIMIT 1");
            $stmt->execute([$country['id']]);
            $state = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "Sample state: {$state['name']} (ID: {$state['id']})\n";
            
            // Get cities for this state
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM spn_cities WHERE state_id = ?");
            $stmt->execute([$state['id']]);
            $cityCount = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Cities for {$state['name']}: {$cityCount['count']}\n";
        }
    }
    
    echo "\n3. Testing specific scenarios...\n";
    
    // Test with country ID 1 (as used in AJAX test)
    $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = 1 LIMIT 5");
    $stmt->execute();
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "States for country ID 1: " . count($states) . "\n";
    foreach ($states as $state) {
        echo "  - {$state['name']} (ID: {$state['id']})\n";
    }
    
    if (!empty($states)) {
        $firstState = $states[0];
        $stmt = $pdo->prepare("SELECT id, name FROM spn_cities WHERE state_id = ? LIMIT 3");
        $stmt->execute([$firstState['id']]);
        $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nCities for state '{$firstState['name']}': " . count($cities) . "\n";
        foreach ($cities as $city) {
            echo "  - {$city['name']} (ID: {$city['id']})\n";
        }
    }
    
    echo "\n4. Checking for data integrity issues...\n";
    
    // Check for orphaned states
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM states s 
        LEFT JOIN countries c ON s.country_id = c.id 
        WHERE c.id IS NULL
    ");
    $orphanedStates = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Orphaned states (no matching country): {$orphanedStates['count']}\n";
    
    // Check for orphaned cities
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM spn_cities c 
        LEFT JOIN states s ON c.state_id = s.id 
        WHERE s.id IS NULL
    ");
    $orphanedCities = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Orphaned cities (no matching state): {$orphanedCities['count']}\n";
    
    // Check for null country_id in states
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM states WHERE country_id IS NULL OR country_id = 0");
    $nullCountryStates = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "States with null/zero country_id: {$nullCountryStates['count']}\n";
    
    // Check for null state_id in cities
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM spn_cities WHERE state_id IS NULL OR state_id = 0");
    $nullStateCities = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Cities with null/zero state_id: {$nullStateCities['count']}\n";
    
    echo "\n5. Testing AJAX response format...\n";
    
    // Simulate the exact query from ProfileCompletionController
    $countryId = 1;
    $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ?");
    $stmt->execute([$countryId]);
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [];
    foreach ($states as $item) {
        $response[] = [
            'id' => $item['id'],
            'text' => $item['name']
        ];
    }
    
    echo "AJAX response format test:\n";
    echo json_encode(['results' => $response], JSON_PRETTY_PRINT) . "\n";
    
    echo "\n6. Testing route conflicts...\n";
    
    // Check if there are any issues with the specific routes
    echo "Testing different country IDs:\n";
    $testCountries = [1, 38, 100, 231]; // Various country IDs
    
    foreach ($testCountries as $cId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM states WHERE country_id = ?");
        $stmt->execute([$cId]);
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt2 = $pdo->prepare("SELECT name FROM countries WHERE id = ?");
        $stmt2->execute([$cId]);
        $countryName = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        $name = $countryName ? $countryName['name'] : 'Unknown';
        echo "Country ID {$cId} ({$name}): {$count['count']} states\n";
    }
    
    echo "\n=== Test completed successfully ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}