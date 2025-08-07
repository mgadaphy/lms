<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple AJAX Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Simple AJAX Test</h1>
    
    <h2>Test 1: Direct ajax_states.php</h2>
    <button onclick="testStates()">Test States for Cameroon (ID: 38)</button>
    <div id="states-result"></div>
    
    <h2>Test 2: Direct ajax_cities.php</h2>
    <button onclick="testCities()">Test Cities for first state</button>
    <div id="cities-result"></div>

    <script>
        function log(target, message) {
            document.getElementById(target).innerHTML += '<p>' + message + '</p>';
        }

        function testStates() {
            log('states-result', 'Testing ajax_states.php for country 38...');
            $.ajax({
                url: 'ajax_states.php',
                method: 'GET',
                data: { id: 38 },
                success: function(data) {
                    log('states-result', 'SUCCESS: ' + JSON.stringify(data, null, 2));
                    // Store first state ID for city test
                    if (data.results && data.results.length > 0) {
                        window.firstStateId = data.results[0].id;
                        log('states-result', 'First state ID: ' + window.firstStateId);
                    }
                },
                error: function(xhr, status, error) {
                    log('states-result', 'ERROR: ' + status + ' - ' + error);
                    log('states-result', 'Response: ' + xhr.responseText);
                }
            });
        }

        function testCities() {
            if (!window.firstStateId) {
                log('cities-result', 'Please test states first to get a state ID');
                return;
            }
            
            log('cities-result', 'Testing ajax_cities.php for state ' + window.firstStateId + '...');
            $.ajax({
                url: 'ajax_cities.php',
                method: 'GET',
                data: { id: window.firstStateId },
                success: function(data) {
                    log('cities-result', 'SUCCESS: ' + JSON.stringify(data, null, 2));
                },
                error: function(xhr, status, error) {
                    log('cities-result', 'ERROR: ' + status + ' - ' + error);
                    log('cities-result', 'Response: ' + xhr.responseText);
                }
            });
        }
    </script>
</body>
</html>