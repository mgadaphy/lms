<?php
// Direct test of the AJAX endpoints
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct AJAX Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Direct AJAX Test</h2>
    
    <button onclick="testStatesEndpoint()">Test States Endpoint</button>
    <button onclick="testCitiesEndpoint()">Test Cities Endpoint</button>
    <button onclick="testRawURL()">Test Raw URL</button>
    
    <div id="results" style="margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;"></div>
    
    <script>
    function testStatesEndpoint() {
        $('#results').html('<p>Testing states endpoint...</p>');
        
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-states&id=38',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('States success:', response);
                $('#results').html('<h3>✓ States Success:</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                console.error('States error:', xhr, status, error);
                $('#results').html('<h3>✗ States Error:</h3><p>Status: ' + status + '</p><p>Error: ' + error + '</p><p>Response Text: ' + xhr.responseText + '</p>');
            }
        });
    }
    
    function testCitiesEndpoint() {
        $('#results').html('<p>Testing cities endpoint...</p>');
        
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-cities&id=1',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Cities success:', response);
                $('#results').html('<h3>✓ Cities Success:</h3><pre>' + JSON.stringify(response, null, 2) + '</pre>');
            },
            error: function(xhr, status, error) {
                console.error('Cities error:', xhr, status, error);
                $('#results').html('<h3>✗ Cities Error:</h3><p>Status: ' + status + '</p><p>Error: ' + error + '</p><p>Response Text: ' + xhr.responseText + '</p>');
            }
        });
    }
    
    function testRawURL() {
        $('#results').html('<p>Testing raw URL access...</p>');
        
        // Test without dataType to see raw response
        $.ajax({
            url: 'fix_ajax_routes.php?action=get-states&id=38',
            type: 'GET',
            success: function(response) {
                console.log('Raw response:', response);
                $('#results').html('<h3>Raw Response:</h3><pre>' + response + '</pre>');
            },
            error: function(xhr, status, error) {
                console.error('Raw error:', xhr, status, error);
                $('#results').html('<h3>✗ Raw Error:</h3><p>Status: ' + status + '</p><p>Error: ' + error + '</p><p>Response Text: ' + xhr.responseText + '</p>');
            }
        });
    }
    </script>
</body>
</html>