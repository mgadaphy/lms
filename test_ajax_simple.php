<?php
// Simple test to verify AJAX endpoints are working
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple AJAX Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Simple AJAX Test</h2>
    
    <div>
        <label>Test Country ID (38 for Cameroon):</label>
        <input type="number" id="country-id" value="38">
        <button onclick="testStates()">Test Get States</button>
    </div>
    
    <div style="margin-top: 20px;">
        <label>Test State ID:</label>
        <input type="number" id="state-id" value="1">
        <button onclick="testCities()">Test Get Cities</button>
    </div>
    
    <div id="results" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;"></div>
    
    <script>
    function testStates() {
        const countryId = $('#country-id').val();
        $('#results').html('<p>Testing states for country ID: ' + countryId + '</p>');
        
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-states',
            type: 'GET',
            data: { id: countryId },
            dataType: 'json',
            success: function(response) {
                console.log('States response:', response);
                let html = '<h3>✓ States loaded successfully:</h3>';
                if (response && response.length > 0) {
                    html += '<ul>';
                    response.forEach(function(state) {
                        html += '<li>ID: ' + state.id + ', Name: ' + state.name + '</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p>No states found</p>';
                }
                $('#results').html(html);
            },
            error: function(xhr, status, error) {
                console.error('States error:', xhr, status, error);
                $('#results').html('<h3>✗ Error loading states:</h3><p>' + error + '</p><p>Status: ' + status + '</p><p>Response: ' + xhr.responseText + '</p>');
            }
        });
    }
    
    function testCities() {
        const stateId = $('#state-id').val();
        $('#results').html('<p>Testing cities for state ID: ' + stateId + '</p>');
        
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-cities',
            type: 'GET',
            data: { id: stateId },
            dataType: 'json',
            success: function(response) {
                console.log('Cities response:', response);
                let html = '<h3>✓ Cities loaded successfully:</h3>';
                if (response && response.length > 0) {
                    html += '<ul>';
                    response.forEach(function(city) {
                        html += '<li>ID: ' + city.id + ', Name: ' + city.name + '</li>';
                    });
                    html += '</ul>';
                } else {
                    html += '<p>No cities found</p>';
                }
                $('#results').html(html);
            },
            error: function(xhr, status, error) {
                console.error('Cities error:', xhr, status, error);
                $('#results').html('<h3>✗ Error loading cities:</h3><p>' + error + '</p><p>Status: ' + status + '</p><p>Response: ' + xhr.responseText + '</p>');
            }
        });
    }
    
    // Test on page load
    $(document).ready(function() {
        $('#results').html('<p>Page loaded. Click buttons to test AJAX endpoints.</p>');
    });
    </script>
</body>
</html>