<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Exact Routes</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .result { background: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 3px; max-height: 300px; overflow-y: auto; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        button { padding: 8px 15px; margin: 5px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Test Exact Profile Completion Routes</h1>
    
    <div class="test-section">
        <h2>Current Environment Info</h2>
        <div id="env-info" class="result">
            <p><strong>Current URL:</strong> <span id="current-url"></span></p>
            <p><strong>Base URL:</strong> <span id="base-url"></span></p>
            <p><strong>Expected States Route:</strong> <code>/profile-completion/get-states</code></p>
            <p><strong>Expected Cities Route:</strong> <code>/profile-completion/get-cities</code></p>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Test Laravel Routes</h2>
        <button onclick="testStatesRoute()">Test States Route</button>
        <button onclick="testCitiesRoute()">Test Cities Route</button>
        <div id="route-results" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test Fallback Scripts</h2>
        <button onclick="testFallbackStates()">Test ajax_states.php</button>
        <button onclick="testFallbackCities()">Test ajax_cities.php</button>
        <div id="fallback-results" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Test Alternative AJAX Routes</h2>
        <button onclick="testAjaxRoutes()">Test ajax/get-states & ajax/get-cities</button>
        <div id="ajax-results" class="result"></div>
    </div>

    <script>
        let firstStateId = null;
        
        function log(target, message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : ''));
            document.getElementById(target).innerHTML += `<p class="${className}">[${timestamp}] ${message}</p>`;
        }
        
        function clearLog(target) {
            document.getElementById(target).innerHTML = '';
        }
        
        function updateEnvInfo() {
            document.getElementById('current-url').textContent = window.location.href;
            document.getElementById('base-url').textContent = window.location.origin;
        }
        
        function formatResponse(response) {
            if (typeof response === 'object') {
                return JSON.stringify(response, null, 2);
            }
            return response;
        }

        function testStatesRoute() {
            clearLog('route-results');
            log('route-results', 'Testing Laravel route: /profile-completion/get-states');
            
            $.ajax({
                url: '/profile-completion/get-states',
                type: 'GET',
                data: { id: 38 }, // Cameroon
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log('route-results', 'SUCCESS: States route is working!', 'success');
                    log('route-results', `Response: <pre>${formatResponse(response)}</pre>`);
                    
                    if (response.results && response.results.length > 0) {
                        firstStateId = response.results[0].id;
                        log('route-results', `Found ${response.results.length} states. First state ID: ${firstStateId}`, 'success');
                    } else {
                        log('route-results', 'No states found in response', 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    log('route-results', `ERROR: States route failed - ${status} (${xhr.status})`, 'error');
                    log('route-results', `Error details: ${error}`);
                    if (xhr.responseText) {
                        log('route-results', `Response: <pre>${xhr.responseText}</pre>`);
                    }
                }
            });
        }

        function testCitiesRoute() {
            if (!firstStateId) {
                log('route-results', 'Please test states route first to get a state ID', 'warning');
                return;
            }
            
            log('route-results', `Testing Laravel route: /profile-completion/get-cities with state ID ${firstStateId}`);
            
            $.ajax({
                url: '/profile-completion/get-cities',
                type: 'GET',
                data: { id: firstStateId },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log('route-results', 'SUCCESS: Cities route is working!', 'success');
                    log('route-results', `Response: <pre>${formatResponse(response)}</pre>`);
                    
                    if (response.results && response.results.length > 0) {
                        log('route-results', `Found ${response.results.length} cities`, 'success');
                    } else {
                        log('route-results', 'No cities found in response', 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    log('route-results', `ERROR: Cities route failed - ${status} (${xhr.status})`, 'error');
                    log('route-results', `Error details: ${error}`);
                    if (xhr.responseText) {
                        log('route-results', `Response: <pre>${xhr.responseText}</pre>`);
                    }
                }
            });
        }

        function testFallbackStates() {
            clearLog('fallback-results');
            log('fallback-results', 'Testing fallback script: ajax_states.php');
            
            $.ajax({
                url: 'ajax_states.php',
                type: 'GET',
                data: { id: 38 },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log('fallback-results', 'SUCCESS: Fallback states script is working!', 'success');
                    log('fallback-results', `Response: <pre>${formatResponse(response)}</pre>`);
                    
                    if (response.results && response.results.length > 0) {
                        if (!firstStateId) firstStateId = response.results[0].id;
                        log('fallback-results', `Found ${response.results.length} states`, 'success');
                    }
                },
                error: function(xhr, status, error) {
                    log('fallback-results', `ERROR: Fallback states script failed - ${status} (${xhr.status})`, 'error');
                    log('fallback-results', `Error details: ${error}`);
                    if (xhr.responseText) {
                        log('fallback-results', `Response: <pre>${xhr.responseText}</pre>`);
                    }
                }
            });
        }

        function testFallbackCities() {
            if (!firstStateId) {
                log('fallback-results', 'Please test states first to get a state ID', 'warning');
                return;
            }
            
            log('fallback-results', `Testing fallback script: ajax_cities.php with state ID ${firstStateId}`);
            
            $.ajax({
                url: 'ajax_cities.php',
                type: 'GET',
                data: { id: firstStateId },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log('fallback-results', 'SUCCESS: Fallback cities script is working!', 'success');
                    log('fallback-results', `Response: <pre>${formatResponse(response)}</pre>`);
                    
                    if (response.results && response.results.length > 0) {
                        log('fallback-results', `Found ${response.results.length} cities`, 'success');
                    }
                },
                error: function(xhr, status, error) {
                    log('fallback-results', `ERROR: Fallback cities script failed - ${status} (${xhr.status})`, 'error');
                    log('fallback-results', `Error details: ${error}`);
                    if (xhr.responseText) {
                        log('fallback-results', `Response: <pre>${xhr.responseText}</pre>`);
                    }
                }
            });
        }

        function testAjaxRoutes() {
            clearLog('ajax-results');
            log('ajax-results', 'Testing alternative AJAX routes: ajax/get-states');
            
            $.ajax({
                url: '/ajax/get-states',
                type: 'GET',
                data: { id: 38 },
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log('ajax-results', 'SUCCESS: Alternative states route is working!', 'success');
                    log('ajax-results', `Response: <pre>${formatResponse(response)}</pre>`);
                },
                error: function(xhr, status, error) {
                    log('ajax-results', `ERROR: Alternative states route failed - ${status} (${xhr.status})`, 'error');
                    log('ajax-results', `Error details: ${error}`);
                }
            });
            
            // Test cities route if we have a state ID
            if (firstStateId) {
                setTimeout(() => {
                    log('ajax-results', `Testing alternative AJAX routes: ajax/get-cities with state ID ${firstStateId}`);
                    
                    $.ajax({
                        url: '/ajax/get-cities',
                        type: 'GET',
                        data: { id: firstStateId },
                        dataType: 'json',
                        timeout: 10000,
                        success: function(response) {
                            log('ajax-results', 'SUCCESS: Alternative cities route is working!', 'success');
                            log('ajax-results', `Response: <pre>${formatResponse(response)}</pre>`);
                        },
                        error: function(xhr, status, error) {
                            log('ajax-results', `ERROR: Alternative cities route failed - ${status} (${xhr.status})`, 'error');
                            log('ajax-results', `Error details: ${error}`);
                        }
                    });
                }, 1000);
            }
        }
        
        // Initialize on page load
        $(document).ready(function() {
            updateEnvInfo();
        });
    </script>
</body>
</html>