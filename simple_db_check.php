<?php
// Simple database check without Laravel framework

// Database configuration (adjust as needed)
$host = 'localhost';
$dbname = 'lms'; // Adjust this to your actual database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Database Connection Successful ===\n";
    
    // Check tables
    $tables = ['countries', 'states', 'cities', 'spn_cities'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✓ Table '$table' exists with {$result['count']} records\n";
        } catch (PDOException $e) {
            echo "✗ Table '$table' does not exist or error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== Testing Data ===\n";
    
    // Check for Cameroon (country ID 38)
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM countries WHERE id = 38 OR name LIKE '%Cameroon%'");
        $stmt->execute();
        $country = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($country) {
            echo "Found country: ID {$country['id']}, Name: {$country['name']}\n";
            $countryId = $country['id'];
            
            // Check states for this country
            $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? LIMIT 3");
            $stmt->execute([$countryId]);
            $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($states) {
                echo "States for country {$countryId}:\n";
                foreach ($states as $state) {
                    echo "  - ID: {$state['id']}, Name: {$state['name']}\n";
                    
                    // Check cities for this state
                    $cityStmt = $pdo->prepare("SELECT id, name FROM spn_cities WHERE state_id = ? LIMIT 2");
                    $cityStmt->execute([$state['id']]);
                    $cities = $cityStmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if ($cities) {
                        echo "    Cities:\n";
                        foreach ($cities as $city) {
                            echo "      - ID: {$city['id']}, Name: {$city['name']}\n";
                        }
                    } else {
                        echo "    No cities found for state {$state['id']}\n";
                    }
                }
            } else {
                echo "No states found for country {$countryId}\n";
            }
        } else {
            echo "Cameroon not found\n";
        }
    } catch (PDOException $e) {
        echo "Error checking data: " . $e->getMessage() . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "\nTrying alternative database names...\n";
    
    // Try common database names
    $dbNames = ['lms', 'infixlms', 'laravel', 'infix_lms'];
    
    foreach ($dbNames as $dbName) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
            echo "✓ Connected to database: $dbName\n";
            break;
        } catch (PDOException $e) {
            echo "✗ Failed to connect to database: $dbName\n";
        }
    }
}

echo "\n=== End Check ===\n";
?>