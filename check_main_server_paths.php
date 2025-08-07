<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Main Server Paths</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .result { background: #f9f9f9; padding: 10px; margin: 10px 0; border-radius: 3px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        button { padding: 8px 15px; margin: 5px; }
    </style>
</head>
<body>
    <h1>Check Main Server Paths</h1>
    
    <div class="test-section">
        <h2>Test Different URL Patterns</h2>
        <button onclick="testAllPaths()">Test All Possible Paths</button>
        <div id="path-results" class="result"></div>
    </div>
    
    <div class="test-section">
        <h2>Current Page Info</h2>
        <div id="page-info" class="result">
            <p><strong>Current URL:</strong> <span id="current-url"></span></p>
            <p><strong>Base URL:</strong> <span id="base-url"></span></p>
            <p><strong>Protocol:</strong> <span id="protocol"></span></p>
            <p><strong>Host:</strong> <span id="host"></span></p>
            <p><strong>Port:</strong> <span id="port"></span></p>
            <p><strong>Pathname:</strong> <span id="pathname"></span></p>
        </div>
    </div>

    <script>
        function log(target, message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : ''));
            document.getElementById(target).innerHTML += `<p class="${className}">[${timestamp}] ${message}</p>`;
        }
        
        function clearLog(target) {
            document.getElementById(target).innerHTML = '';
        }
        
        function updatePageInfo() {
            document.getElementById('current-url').textContent = window.location.href;
            document.getElementById('base-url').textContent = window.location.origin;
            document.getElementById('protocol').textContent = window.location.protocol;
            document.getElementById('host').textContent = window.location.hostname;
            document.getElementById('port').textContent = window.location.port || '80 (default)';
            document.getElementById('pathname').textContent = window.location.pathname;
        }

        function testPath(url, description) {
            return new Promise((resolve) => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: { id: 38 },
                    timeout: 5000,
                    success: function(response) {
                        log('path-results', `✓ ${description}: ${url} - SUCCESS`, 'success');
                        resolve({ url, success: true, response });
                    },
                    error: function(xhr, status, error) {
                        const statusCode = xhr.status || 'Unknown';
                        log('path-results', `✗ ${description}: ${url} - ERROR (${statusCode})`, 'error');
                        resolve({ url, success: false, status: statusCode, error });
                    }
                });
            });
        }

        async function testAllPaths() {
            clearLog('path-results');
            log('path-results', 'Testing various URL patterns for state loading...');
            
            const baseUrl = window.location.origin;
            const testUrls = [
                // Laravel routes
                { url: '/profile-completion/get-states', desc: 'Laravel Route (Root)' },
                { url: '/lms/profile-completion/get-states', desc: 'Laravel Route (LMS subfolder)' },
                { url: baseUrl + '/profile-completion/get-states', desc: 'Laravel Route (Full URL)' },
                { url: baseUrl + '/lms/profile-completion/get-states', desc: 'Laravel Route (Full URL + LMS)' },
                
                // Standalone scripts
                { url: '/ajax_states.php', desc: 'Standalone Script (Root)' },
                { url: '/lms/ajax_states.php', desc: 'Standalone Script (LMS subfolder)' },
                { url: '/lms/public/ajax_states.php', desc: 'Standalone Script (LMS/public)' },
                { url: baseUrl + '/ajax_states.php', desc: 'Standalone Script (Full URL)' },
                { url: baseUrl + '/lms/ajax_states.php', desc: 'Standalone Script (Full URL + LMS)' },
                { url: baseUrl + '/lms/public/ajax_states.php', desc: 'Standalone Script (Full URL + LMS/public)' }
            ];
            
            for (const test of testUrls) {
                await testPath(test.url, test.desc);
                // Small delay between requests
                await new Promise(resolve => setTimeout(resolve, 200));
            }
            
            log('path-results', 'Testing completed. Check results above.', 'warning');
        }
        
        // Initialize page info on load
        $(document).ready(function() {
            updatePageInfo();
        });
    </script>
</body>
</html>