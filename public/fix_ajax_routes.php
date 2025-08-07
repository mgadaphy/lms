<?php
/**
 * Fix for AJAX routes issue in profile-completion
 *
 * The problem: The fallback route in tenant.php is intercepting AJAX requests
 * because the profile-completion AJAX routes aren't being matched properly.
 *
 * Solution: Create standalone AJAX endpoints that bypass Laravel routing
 */

// Include database configuration
require_once '../config/database.php';

try {
    // Get database configuration
    $config = include '../config/database.php';
    $dbConfig = $config['connections']['mysql'];

    // Create PDO connection
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4",
        $dbConfig['username'],
        $dbConfig['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Handle AJAX requests
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');

        switch ($_GET['action']) {
            case 'get-states':
                $countryId = intval($_GET['id'] ?? 0);
                if ($countryId > 0) {
                    $stmt = $pdo->prepare("SELECT id, name FROM states WHERE country_id = ? ORDER BY name");
                    $stmt->execute([$countryId]);
                    $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($states);
                } else {
                    echo json_encode([]);
                }
                break;

            case 'get-cities':
                $stateId = intval($_GET['id'] ?? 0);
                if ($stateId > 0) {
                    // Try spn_cities table first (as used in controller)
                    $stmt = $pdo->prepare("SELECT id, name FROM spn_cities WHERE state_id = ? ORDER BY name");
                    $stmt->execute([$stateId]);
                    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    echo json_encode($cities);
                } else {
                    echo json_encode([]);
                }
                break;

            default:
                echo json_encode(['error' => 'Invalid action']);
        }
        exit;
    }

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// If not an AJAX request, show test interface
?>
<!DOCTYPE html>
<html>
<head>
    <title>AJAX Routes Fix</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>AJAX Routes Fix Test</h1>

    <h2>Test Get States (Country ID: 38 - Cameroon)</h2>
    <button onclick="testStates()">Test Get States</button>
    <div id="states-result"></div>

    <h2>Test Get Cities (State ID: 1)</h2>
    <button onclick="testCities()">Test Get Cities</button>
    <div id="cities-result"></div>

    <script>
    function testStates() {
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-states&id=38',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#states-result').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                $('#states-result').html('<p style="color: red;">Error: ' + error + '</p>');
            }
        });
    }

    function testCities() {
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-cities&id=1',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#cities-result').html('<pre>' + JSON.stringify(data, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                $('#cities-result').html('<p style="color: red;">Error: ' + error + '</p>');
            }
        });
    }
    </script>
</body>
</html>
