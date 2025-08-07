<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Completion Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select { width: 300px; padding: 8px; }
        .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Profile Completion Test</h1>
    
    <div class="form-group">
        <label for="country">Country:</label>
        <select id="country" name="country">
            <option value="">Select Country</option>
            <option value="38">Cameroon</option>
            <option value="39">Canada</option>
            <option value="1">Afghanistan</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="state">State:</label>
        <select id="state" name="state" disabled>
            <option value="">Select State</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="city">City:</label>
        <select id="city" name="city" disabled>
            <option value="">Select City</option>
        </select>
    </div>
    
    <div class="debug">
        <h3>Debug Information:</h3>
        <p><strong>Selected Country:</strong> <span id="debug-country">None</span></p>
        <p><strong>Selected State:</strong> <span id="debug-state">None</span></p>
        <p><strong>Selected City:</strong> <span id="debug-city">None</span></p>
    </div>
    
    <div class="debug">
        <h3>Test Log:</h3>
        <div id="test-log"></div>
    </div>

    <script>
        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const className = type === 'error' ? 'error' : (type === 'success' ? 'success' : '');
            $('#test-log').append(`<p class="${className}">[${timestamp}] ${message}</p>`);
        }
        
        function updateSelections() {
            // Get country text
            const $country = $('#country');
            let countryText = 'None';
            if ($country.val()) {
                countryText = $country.find('option:selected').text();
            }
            
            // Get state text
            const $state = $('#state');
            let stateText = 'None';
            if ($state.val()) {
                stateText = $state.find('option:selected').text();
            }
            
            // Get city text
            const $city = $('#city');
            let cityText = 'None';
            if ($city.val()) {
                cityText = $city.find('option:selected').text();
            }
            
            $('#debug-country').text(countryText);
            $('#debug-state').text(stateText);
            $('#debug-city').text(cityText);
        }

        // Load states for a given country
        function loadStates(countryId, selectedStateId = null, callback = null) {
            const $state = $('#state');
            const $city = $('#city');
            
            $state.prop('disabled', true).empty().append('<option value="">Select State</option>');
            $city.prop('disabled', true).empty().append('<option value="">Select City</option>');
            
            if (!countryId) {
                $state.prop('disabled', false);
                $city.prop('disabled', false);
                updateSelections();
                if (callback) callback();
                return;
            }

            log(`Loading states for country ID: ${countryId}`);
            
            // Test standalone script first
            $.ajax({
                url: 'ajax_states.php',
                type: 'GET',
                data: { id: countryId },
                dataType: 'json',
                success: function(response) {
                    log(`States loaded successfully: ${response.results ? response.results.length : 0} states found`, 'success');
                    $state.prop('disabled', false).empty().append('<option value="">Select State</option>');
                    
                    if (response.results && response.results.length > 0) {
                        response.results.forEach(function(state) {
                            $state.append(`<option value="${state.id}">${state.text}</option>`);
                        });
                        
                        if (selectedStateId) {
                            $state.val(selectedStateId).trigger('change');
                        }
                    }
                    
                    updateSelections();
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    log(`Error loading states: ${status} - ${error}`, 'error');
                    $state.prop('disabled', false).html('<option value="">Error loading states</option>');
                    updateSelections();
                    if (callback) callback();
                }
            });
        }

        // Load cities for a given state
        function loadCities(stateId, selectedCityId = null, callback = null) {
            const $city = $('#city');
            
            $city.prop('disabled', true).empty().append('<option value="">Select City</option>');
            
            if (!stateId) {
                $city.prop('disabled', false);
                updateSelections();
                if (callback) callback();
                return;
            }

            log(`Loading cities for state ID: ${stateId}`);
            
            // Test standalone script
            $.ajax({
                url: 'ajax_cities.php',
                type: 'GET',
                data: { id: stateId },
                dataType: 'json',
                success: function(response) {
                    log(`Cities loaded successfully: ${response.results ? response.results.length : 0} cities found`, 'success');
                    $city.prop('disabled', false).empty().append('<option value="">Select City</option>');
                    
                    if (response.results && response.results.length > 0) {
                        response.results.forEach(function(city) {
                            $city.append(`<option value="${city.id}">${city.text}</option>`);
                        });
                        
                        if (selectedCityId) {
                            $city.val(selectedCityId).trigger('change');
                        }
                    }
                    
                    updateSelections();
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    log(`Error loading cities: ${status} - ${error}`, 'error');
                    $city.prop('disabled', false).html('<option value="">Error loading cities</option>');
                    updateSelections();
                    if (callback) callback();
                }
            });
        }

        // Event handlers
        $('#country').on('change', function() {
            const countryId = $(this).val();
            log(`Country changed to: ${countryId}`);
            loadStates(countryId, null, function() {
                $('#city').val('').trigger('change');
            });
        });

        $('#state').on('change', function() {
            const stateId = $(this).val();
            log(`State changed to: ${stateId}`);
            loadCities(stateId, null);
        });

        $('#city').on('change', function() {
            updateSelections();
        });

        // Initialize
        $(document).ready(function() {
            log('Profile completion test initialized');
            updateSelections();
        });
    </script>
</body>
</html>