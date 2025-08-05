<?php
// Simple test to verify routes are working
echo "Testing Profile Completion Routes...\n\n";

// Test 1: Check if we can access the profile completion page
echo "1. Testing profile completion page access...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/lms/profile-completion");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response length: " . strlen($response) . " bytes\n";
if ($httpCode == 200) {
    echo "✓ Profile completion page is accessible\n";
} else {
    echo "✗ Profile completion page returned HTTP $httpCode\n";
}

// Test 2: Test the getStates route
echo "\n2. Testing getStates route...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/lms/profile-completion/get-states?id=38");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['results'])) {
        echo "✓ getStates route working - found " . count($data['results']) . " states\n";
        if (count($data['results']) > 0) {
            echo "  First state: " . $data['results'][0]['text'] . "\n";
        }
    } else {
        echo "✗ getStates returned invalid JSON format\n";
    }
} else {
    echo "✗ getStates route returned HTTP $httpCode\n";
}

// Test 3: Test the getCities route (if we have a state ID)
echo "\n3. Testing getCities route...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost/lms/profile-completion/get-cities?id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data && isset($data['results'])) {
        echo "✓ getCities route working - found " . count($data['results']) . " cities\n";
        if (count($data['results']) > 0) {
            echo "  First city: " . $data['results'][0]['text'] . "\n";
        }
    } else {
        echo "✗ getCities returned invalid JSON format\n";
    }
} else {
    echo "✗ getCities route returned HTTP $httpCode\n";
}

echo "\n=== Route Testing Complete ===\n"; 