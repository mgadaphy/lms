<?php
// Direct Test - No HTML, No Redirects, Just Database Testing
echo "<h1>🔍 Direct Database & AJAX Test</h1>";

// Test 1: Database Connection
echo "<h2>Test 1: Database Connection</h2>";
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    
    // Test 2: Countries Query
    echo "<h2>Test 2: Countries Query</h2>";
    $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name LIMIT 10");
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✅ Found " . count($countries) . " countries:</p>";
    echo "<ul>";
    foreach ($countries as $country) {
        echo "<li>ID: {$country['id']} - {$country['name']}</li>";
    }
    echo "</ul>";
    
    // Test 3: States Query for Cameroon (ID=38)
    echo "<h2>Test 3: States for Cameroon (ID=38)</h2>";
    $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? ORDER BY name LIMIT 10");
    $stmt->execute([38]);
    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✅ Found " . count($states) . " states for Cameroon:</p>";
    echo "<ul>";
    foreach ($states as $state) {
        echo "<li>ID: {$state['id']} - {$state['name']}</li>";
    }
    echo "</ul>";
    
    // Test 4: Cities Query for State 659
    echo "<h2>Test 4: Cities for State 659</h2>";
    $stmt = $pdo->prepare("SELECT id, name FROM spn_cities WHERE state_id = ? ORDER BY name LIMIT 10");
    $stmt->execute([659]);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<p style='color: green;'>✅ Found " . count($cities) . " cities for State 659:</p>";
    echo "<ul>";
    foreach ($cities as $city) {
        echo "<li>ID: {$city['id']} - {$city['name']}</li>";
    }
    echo "</ul>";
    
    // Test 5: Test AJAX endpoints directly
    echo "<h2>Test 5: AJAX Endpoints</h2>";
    
    // Test ajax_states.php
    echo "<h3>Testing ajax_states.php:</h3>";
    $url = "http://localhost/lms/ajax_states.php?id=38&search=";
    $response = file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        echo "<p style='color: green;'>✅ ajax_states.php working! Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ ajax_states.php failed!</p>";
    }
    
    // Test ajax_cities.php
    echo "<h3>Testing ajax_cities.php:</h3>";
    $url = "http://localhost/lms/ajax_cities.php?id=659&search=";
    $response = file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        echo "<p style='color: green;'>✅ ajax_cities.php working! Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ ajax_cities.php failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 3px; overflow-x: auto; }
</style> 