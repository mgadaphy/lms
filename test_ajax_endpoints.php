<?php
// Test AJAX endpoints directly
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX Endpoints</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Testing AJAX Endpoints</h1>
    
    <h2>Test 1: Direct ajax_states.php</h2>
    <button onclick="testStates()">Test States for Cameroon (ID: 38)</button>
    <div id="states-result"></div>
    
    <h2>Test 2: Direct ajax_cities.php</h2>
    <button onclick="testCities()">Test Cities for first state</button>
    <div id="cities-result"></div>
    
    <h2>Test 3: Laravel Routes</h2>
    <button onclick="testLaravelStates()">Test Laravel getStates</button>
    <div id="laravel-states-result"></div>
    
    <button onclick="testLaravelCities()">Test Laravel getCities</button>
    <div id="laravel-cities-result"></div>
    
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
                },
                error: function(xhr, status, error) {
                    log('states-result', 'ERROR: ' + status + ' - ' + error);
                    log('states-result', 'Response: ' + xhr.responseText);
                }
            });
        }
        
        function testCities() {
            // First get a state ID
            $.ajax({
                url: 'ajax_states.php',
                method: 'GET',
                data: { id: 38 },
                success: function(data) {
                    if (data.results && data.results.length > 0) {
                        const stateId = data.results[0].id;
                        log('cities-result', 'Testing ajax_cities.php for state ' + stateId + '...');
                        $.ajax({
                            url: 'ajax_cities.php',
                            method: 'GET',
                            data: { id: stateId },
                            success: function(cityData) {
                                log('cities-result', 'SUCCESS: ' + JSON.stringify(cityData, null, 2));
                            },
                            error: function(xhr, status, error) {
                                log('cities-result', 'ERROR: ' + status + ' - ' + error);
                                log('cities-result', 'Response: ' + xhr.responseText);
                            }
                        });
                    } else {
                        log('cities-result', 'No states found for country 38');
                    }
                },
                error: function(xhr, status, error) {
                    log('cities-result', 'ERROR getting states: ' + status + ' - ' + error);
                }
            });
        }
        
        function testLaravelStates() {
            log('laravel-states-result', 'Testing Laravel route for states...');
            $.ajax({
                url: '/profile-completion/get-states',
                method: 'GET',
                data: { id: 38 },
                success: function(data) {
                    log('laravel-states-result', 'SUCCESS: ' + JSON.stringify(data, null, 2));
                },
                error: function(xhr, status, error) {
                    log('laravel-states-result', 'ERROR: ' + status + ' - ' + error);
                    log('laravel-states-result', 'Response: ' + xhr.responseText);
                }
            });
        }
        
        function testLaravelCities() {
            // First get a state ID
            $.ajax({
                url: '/profile-completion/get-states',
                method: 'GET',
                data: { id: 38 },
                success: function(data) {
                    if (data.results && data.results.length > 0) {
                        const stateId = data.results[0].id;
                        log('laravel-cities-result', 'Testing Laravel route for cities with state ' + stateId + '...');
                        $.ajax({
                            url: '/profile-completion/get-cities',
                            method: 'GET',
                            data: { id: stateId },
                            success: function(cityData) {
                                log('laravel-cities-result', 'SUCCESS: ' + JSON.stringify(cityData, null, 2));
                            },
                            error: function(xhr, status, error) {
                                log('laravel-cities-result', 'ERROR: ' + status + ' - ' + error);
                                log('laravel-cities-result', 'Response: ' + xhr.responseText);
                            }
                        });
                    } else {
                        log('laravel-cities-result', 'No states found for country 38');
                    }
                },
                error: function(xhr, status, error) {
                    log('laravel-cities-result', 'ERROR getting states: ' + status + ' - ' + error);
                }
            });
        }
    </script>
</body>
</html>