<?php
// Test file to verify AJAX endpoints work
echo "<h1>Testing AJAX Endpoints</h1>";

// Test 1: Test ajax_states.php with country ID 38
echo "<h2>Test 1: States for Cameroon (ID=38)</h2>";
$_GET['id'] = '38';
$_GET['search'] = '';

echo "<p>Calling ajax_states.php with id=38...</p>";
ob_start();
include 'ajax_states.php';
$result = ob_get_clean();
echo "<pre>" . htmlspecialchars($result) . "</pre>";

// Test 2: Test ajax_cities.php with state ID 659
echo "<h2>Test 2: Cities for State 659</h2>";
$_GET['id'] = '659';
$_GET['search'] = '';

echo "<p>Calling ajax_cities.php with id=659...</p>";
ob_start();
include 'ajax_cities.php';
$result = ob_get_clean();
echo "<pre>" . htmlspecialchars($result) . "</pre>";

echo "<h2>Test Complete!</h2>";
?> 