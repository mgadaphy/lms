<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Main Server Profile Completion</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .result { background: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 3px; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 8px 15px; margin: 5px; }
    </style>
</head>
<body>
    <h1>Debug Main Server Profile Completion</h1>
    
    <div class="test-section">
        <h2>Test 1: Laravel Route - getStates</h2>
        <button onclick="testLaravelStates()">Test Laravel getStates Route</button>
        <div id="laravel-states-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 2: Standalone Script - ajax_states.php</h2>
        <button onclick="testStandaloneStates()">Test Standalone ajax_states.php</button>
        <div id="standalone-states-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 3: Laravel Route - getCities</h2>
        <button onclick="testLaravelCities()">Test Laravel getCities Route</button>
        <div id="laravel-cities-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 4: Standalone Script - ajax_cities.php</h2>
        <button onclick="testStandaloneCities()">Test Standalone ajax_cities.php</button>
        <div id="standalone-cities-result" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test 5: Check Network Requests</h2>
        <p>Open browser developer tools (F12) and check the Network tab while testing the dropdowns on the actual profile completion page.</p>
    </div>

    <script>
        function log(target, message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : (type === 'success' ? 'success' : '');
            document.getElementById(target).innerHTML += `<p class="${className}">[${timestamp}] ${message}</p>`;
        }
        
        function clearLog(target) {
            document.getElementById(target).innerHTML = '';
        }

        function testLaravelStates() {
            clearLog('laravel-states-result');
            log('laravel-states-result', 'Testing Laravel route: /profile-completion/get-states for country ID 38 (Cameroon)...');
            
            $.ajax({
                url: '/profile-completion/get-states',
                type: 'GET',
                data: { id: 38 },
                dataType: 'json',
                success: function(response) {
                    log('laravel-states-result', 'SUCCESS: Received response', 'success');
                    log('laravel-states-result', 'Response: ' + JSON.stringify(response, null, 2));
                    if (response.results && response.results.length > 0) {
                        log('laravel-states-result', `Found ${response.results.length} states`, 'success');
                    } else {
                        log('laravel-states-result', 'No states found in response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    log('laravel-states-result', `ERROR: ${status} - ${error}`, 'error');
                    log('laravel-states-result', `Status Code: ${xhr.status}`);
                    log('laravel-states-result', `Response Text: ${xhr.responseText}`);
                }
            });
        }

        function testStandaloneStates() {
            clearLog('standalone-states-result');
            log('standalone-states-result', 'Testing standalone script: ajax_states.php for country ID 38 (Cameroon)...');
            
            $.ajax({
                url: 'ajax_states.php',
                type: 'GET',
                data: { id: 38 },
                dataType: 'json',
                success: function(response) {
                    log('standalone-states-result', 'SUCCESS: Received response', 'success');
                    log('standalone-states-result', 'Response: ' + JSON.stringify(response, null, 2));
                    if (response.results && response.results.length > 0) {
                        log('standalone-states-result', `Found ${response.results.length} states`, 'success');
                        window.firstStateId = response.results[0].id;
                        log('standalone-states-result', `First state ID stored: ${window.firstStateId}`);
                    } else {
                        log('standalone-states-result', 'No states found in response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    log('standalone-states-result', `ERROR: ${status} - ${error}`, 'error');
                    log('standalone-states-result', `Status Code: ${xhr.status}`);
                    log('standalone-states-result', `Response Text: ${xhr.responseText}`);
                }
            });
        }

        function testLaravelCities() {
            clearLog('laravel-cities-result');
            
            if (!window.firstStateId) {
                log('laravel-cities-result', 'Please test standalone states first to get a state ID', 'error');
                return;
            }
            
            log('laravel-cities-result', `Testing Laravel route: /profile-completion/get-cities for state ID ${window.firstStateId}...`);
            
            $.ajax({
                url: '/profile-completion/get-cities',
                type: 'GET',
                data: { id: window.firstStateId },
                dataType: 'json',
                success: function(response) {
                    log('laravel-cities-result', 'SUCCESS: Received response', 'success');
                    log('laravel-cities-result', 'Response: ' + JSON.stringify(response, null, 2));
                    if (response.results && response.results.length > 0) {
                        log('laravel-cities-result', `Found ${response.results.length} cities`, 'success');
                    } else {
                        log('laravel-cities-result', 'No cities found in response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    log('laravel-cities-result', `ERROR: ${status} - ${error}`, 'error');
                    log('laravel-cities-result', `Status Code: ${xhr.status}`);
                    log('laravel-cities-result', `Response Text: ${xhr.responseText}`);
                }
            });
        }

        function testStandaloneCities() {
            clearLog('standalone-cities-result');
            
            if (!window.firstStateId) {
                log('standalone-cities-result', 'Please test standalone states first to get a state ID', 'error');
                return;
            }
            
            log('standalone-cities-result', `Testing standalone script: ajax_cities.php for state ID ${window.firstStateId}...`);
            
            $.ajax({
                url: 'ajax_cities.php',
                type: 'GET',
                data: { id: window.firstStateId },
                dataType: 'json',
                success: function(response) {
                    log('standalone-cities-result', 'SUCCESS: Received response', 'success');
                    log('standalone-cities-result', 'Response: ' + JSON.stringify(response, null, 2));
                    if (response.results && response.results.length > 0) {
                        log('standalone-cities-result', `Found ${response.results.length} cities`, 'success');
                    } else {
                        log('standalone-cities-result', 'No cities found in response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    log('standalone-cities-result', `ERROR: ${status} - ${error}`, 'error');
                    log('standalone-cities-result', `Status Code: ${xhr.status}`);
                    log('standalone-cities-result', `Response Text: ${xhr.responseText}`);
                }
            });
        }
    </script>
</body>
</html>