<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Laravel Routes Direct Access</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .result { background: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 3px; max-height: 400px; overflow-y: auto; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        button { padding: 8px 15px; margin: 5px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px; }
        .url-test { margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Test Laravel Routes Direct Access</h1>
    
    <div class="test-section">
        <h2>Environment Information</h2>
        <div class="result">
            <p><strong>Current URL:</strong> <span id="current-url"></span></p>
            <p><strong>Base URL:</strong> <span id="base-url"></span></p>
            <p><strong>Test Country ID:</strong> 38 (Cameroon)</p>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Direct URL Tests</h2>
        <p>Click the links below to test direct access to Laravel routes:</p>
        
        <div class="url-test">
            <strong>States Route:</strong><br>
            <a href="/profile-completion/get-states?id=38" target="_blank">/profile-completion/get-states?id=38</a>
            <button onclick="testUrl('/profile-completion/get-states?id=38', 'states-direct')">Test via AJAX</button>
            <div id="states-direct" class="result" style="display:none;"></div>
        </div>
        
        <div class="url-test">
            <strong>Cities Route (will use first state ID from states):</strong><br>
            <span id="cities-url">Will be populated after states test</span>
            <button onclick="testCitiesUrl()" id="cities-btn" disabled>Test Cities via AJAX</button>
            <div id="cities-direct" class="result" style="display:none;"></div>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Alternative Route Tests</h2>
        
        <div class="url-test">
            <strong>Alternative States Route:</strong><br>
            <a href="/lms/profile-completion/get-states?id=38" target="_blank">/lms/profile-completion/get-states?id=38</a>
            <button onclick="testUrl('/lms/profile-completion/get-states?id=38', 'alt-states')">Test via AJAX</button>
            <div id="alt-states" class="result" style="display:none;"></div>
        </div>
        
        <div class="url-test">
            <strong>Public Folder Route:</strong><br>
            <a href="/lms/public/profile-completion/get-states?id=38" target="_blank">/lms/public/profile-completion/get-states?id=38</a>
            <button onclick="testUrl('/lms/public/profile-completion/get-states?id=38', 'public-states')">Test via AJAX</button>
            <div id="public-states" class="result" style="display:none;"></div>
        </div>
    </div>
    
    <div class="test-section">
        <h2>Fallback Script Tests</h2>
        
        <div class="url-test">
            <strong>Standalone States Script:</strong><br>
            <a href="ajax_states.php?id=38" target="_blank">ajax_states.php?id=38</a>
            <button onclick="testUrl('ajax_states.php?id=38', 'fallback-states')">Test via AJAX</button>
            <div id="fallback-states" class="result" style="display:none;"></div>
        </div>
        
        <div class="url-test">
            <strong>Standalone Cities Script:</strong><br>
            <span id="fallback-cities-url">Will be populated after states test</span>
            <button onclick="testFallbackCities()" id="fallback-cities-btn" disabled>Test Cities via AJAX</button>
            <div id="fallback-cities" class="result" style="display:none;"></div>
        </div>
    </div>

    <script>
        let firstStateId = null;
        
        function log(target, message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : ''));
            const targetEl = document.getElementById(target);
            targetEl.style.display = 'block';
            targetEl.innerHTML += `<p class="${className}">[${timestamp}] ${message}</p>`;
        }
        
        function clearLog(target) {
            const targetEl = document.getElementById(target);
            targetEl.innerHTML = '';
            targetEl.style.display = 'none';
        }
        
        function formatResponse(response) {
            if (typeof response === 'object') {
                return JSON.stringify(response, null, 2);
            }
            return response;
        }
        
        function testUrl(url, targetId) {
            clearLog(targetId);
            log(targetId, `Testing URL: ${url}`);
            
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    log(targetId, 'SUCCESS: Route is working!', 'success');
                    log(targetId, `Response: <pre>${formatResponse(response)}</pre>`);
                    
                    if (response.results && response.results.length > 0) {
                        log(targetId, `Found ${response.results.length} items`, 'success');
                        
                        // If this is a states response, store the first state ID
                        if (url.includes('states') && !firstStateId) {
                            firstStateId = response.results[0].id;
                            updateCitiesButtons();
                        }
                    } else {
                        log(targetId, 'No items found in response', 'warning');
                    }
                },
                error: function(xhr, status, error) {
                    log(targetId, `ERROR: Route failed - ${status} (${xhr.status})`, 'error');
                    log(targetId, `Error details: ${error}`);
                    if (xhr.responseText) {
                        log(targetId, `Response: <pre>${xhr.responseText.substring(0, 500)}${xhr.responseText.length > 500 ? '...' : ''}</pre>`);
                    }
                }
            });
        }
        
        function updateCitiesButtons() {
            if (firstStateId) {
                document.getElementById('cities-url').innerHTML = `<a href="/profile-completion/get-cities?id=${firstStateId}" target="_blank">/profile-completion/get-cities?id=${firstStateId}</a>`;
                document.getElementById('cities-btn').disabled = false;
                
                document.getElementById('fallback-cities-url').innerHTML = `<a href="ajax_cities.php?id=${firstStateId}" target="_blank">ajax_cities.php?id=${firstStateId}</a>`;
                document.getElementById('fallback-cities-btn').disabled = false;
            }
        }
        
        function testCitiesUrl() {
            if (firstStateId) {
                testUrl(`/profile-completion/get-cities?id=${firstStateId}`, 'cities-direct');
            }
        }
        
        function testFallbackCities() {
            if (firstStateId) {
                testUrl(`ajax_cities.php?id=${firstStateId}`, 'fallback-cities');
            }
        }
        
        // Initialize on page load
        $(document).ready(function() {
            document.getElementById('current-url').textContent = window.location.href;
            document.getElementById('base-url').textContent = window.location.origin;
        });
    </script>
</body>
</html>