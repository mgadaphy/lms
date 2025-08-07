<?php
/**
 * Debug script to test AJAX endpoints directly
 */

// Test the fix_ajax_routes.php endpoints
echo "<h1>Debug AJAX Endpoints</h1>";

// Test get-states endpoint
echo "<h2>Testing get-states endpoint (Country ID: 38 - Cameroon)</h2>";
$url = 'http://localhost/lms/fix_ajax_routes.php?action=get-states&id=38';
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response:</p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>✓ JSON is valid. Found " . count($data) . " states.</p>";
        if (count($data) > 0) {
            echo "<p>First state: ID=" . $data[0]['id'] . ", Name=" . $data[0]['name'] . "</p>";
        }
    } else {
        echo "<p>✗ Invalid JSON response</p>";
    }
} else {
    echo "<p>✗ HTTP Error: $httpCode</p>";
}

// Test get-cities endpoint
echo "<h2>Testing get-cities endpoint (State ID: 1)</h2>";
$url = 'http://localhost/lms/fix_ajax_routes.php?action=get-cities&id=1';
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response:</p>";
echo "<pre>" . htmlspecialchars($response) . "</pre>";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>✓ JSON is valid. Found " . count($data) . " cities.</p>";
        if (count($data) > 0) {
            echo "<p>First city: ID=" . $data[0]['id'] . ", Name=" . $data[0]['name'] . "</p>";
        }
    } else {
        echo "<p>✗ Invalid JSON response</p>";
    }
} else {
    echo "<p>✗ HTTP Error: $httpCode</p>";
}

// Test Laravel routes
echo "<h2>Testing Laravel Routes</h2>";

// Test Laravel get-states route
echo "<h3>Laravel get-states route</h3>";
$url = 'http://localhost/lms/profile-completion/get-states?id=38';
echo "<p>URL: <a href='$url' target='_blank'>$url</a></p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>HTTP Code: $httpCode</p>";
echo "<p>Response (first 500 chars):</p>";
echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "<p>✓ Laravel route working - JSON is valid</p>";
    } else {
        echo "<p>✗ Laravel route returning HTML instead of JSON (fallback route issue)</p>";
    }
} else {
    echo "<p>✗ Laravel route HTTP Error: $httpCode</p>";
}

echo "<hr>";
echo "<h2>JavaScript Test</h2>";
echo "<p>Select a country to test the AJAX calls:</p>";
echo "<select id='country-test'>";
echo "<option value=''>Select Country</option>";
echo "<option value='38'>Cameroon</option>";
echo "<option value='1'>USA</option>";
echo "</select>";
echo "<br><br>";
echo "<select id='state-test' disabled>";
echo "<option value=''>Select State</option>";
echo "</select>";
echo "<br><br>";
echo "<div id='debug-output'></div>";

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#country-test').on('change', function() {
    const countryId = $(this).val();
    const $state = $('#state-test');
    $state.prop('disabled', true).empty().append('<option value="">Loading...</option>');
    $('#debug-output').html('<p>Testing AJAX call...</p>');
    
    if (!countryId) {
        $state.prop('disabled', false).empty().append('<option value="">Select State</option>');
        return;
    }
    
    // Test the fix_ajax_routes.php endpoint
    $.ajax({
        url: 'fix_ajax_routes.php?action=get-states',
        type: 'GET',
        data: { id: countryId },
        dataType: 'json',
        success: function(response) {
            $state.prop('disabled', false).empty().append('<option value="">Select State</option>');
            if (response && response.length > 0) {
                response.forEach(function(state) {
                    $state.append(`<option value="${state.id}">${state.name}</option>`);
                });
                $('#debug-output').html('<p>✓ Successfully loaded ' + response.length + ' states</p>');
            } else {
                $('#debug-output').html('<p>✗ No states found</p>');
            }
        },
        error: function(xhr, status, error) {
            $('#debug-output').html('<p>✗ AJAX Error: ' + error + '</p>');
            $state.prop('disabled', false).empty().append('<option value="">Error loading states</option>');
        }
    });
});
</script>
<?php
?>