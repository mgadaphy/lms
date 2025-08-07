/**
 * Profile Completion Form JavaScript
 * Handles form submission, AJAX calls, and dropdown dependencies
 */

$(document).ready(function() {
    // Setup CSRF token for AJAX requests - use the correct meta tag
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    // Initialize form functionality
    initializeProfileForm();
});

function initializeProfileForm() {
    // Handle country change
    $('#country').on('change', function() {
        const countryId = $(this).val();
        const currentStateId = $('#state').val();

        if (countryId) {
            loadStates(countryId);
            // Clear dependent dropdowns only if country actually changed
            if (currentStateId) {
                $('#state').val('').trigger('change');
            }
            $('#city').empty().append('<option value="">Select City</option>');
        } else {
            clearDropdowns(['state', 'city']);
        }
    });

    // Handle state change
    $('#state').on('change', function() {
        const stateId = $(this).val();
        const currentCityId = $('#city').val();

        if (stateId) {
            loadCities(stateId);
            // Clear city dropdown only if state actually changed
            if (currentCityId) {
                $('#city').val('');
            } else {
                $('#city').empty().append('<option value="">Select City</option>');
            }
        } else {
            clearDropdowns(['city']);
        }
    });

    // Handle form submission
    $('#profile-completion-form').on('submit', function(e) {
        e.preventDefault();
        submitProfileForm();
    });
}

function loadStates(countryId) {
    const stateSelect = $('#state');
    const citySelect = $('#city');

    // Show loading state
    stateSelect.prop('disabled', true).html('<option value="">Loading states...</option>');
    citySelect.prop('disabled', true).html('<option value="">Select City</option>');

    // Make AJAX request to working route
    $.ajax({
        url: '/ajaxCounterState', // Use the working route directly
        type: 'GET',
        data: {
            id: countryId,
            search: '',
            page: 1
        },
        dataType: 'json',
        success: function(response) {
            populateDropdown(stateSelect, response, 'Select State');
            stateSelect.prop('disabled', false);
            console.log('States loaded successfully:', response.results ? response.results.length : response.length, 'states');
        },
        error: function(xhr, status, error) {
            console.error('Error loading states:', error, xhr.responseText);
            handleDropdownError(stateSelect, 'Error loading states');

            // Show user-friendly error message
            showErrorMessage('Unable to load states. Please try again.');

            // Try fallback if available
            tryFallbackStates(countryId);
        }
    });
}

function loadCities(stateId) {
    const citySelect = $('#city');

    // Show loading state
    citySelect.prop('disabled', true).html('<option value="">Loading cities...</option>');

    // Make AJAX request to working route
    $.ajax({
        url: '/ajaxCounterCity', // Use the working route directly
        type: 'GET',
        data: {
            id: stateId,
            search: '',
            page: 1
        },
        dataType: 'json',
        success: function(response) {
            populateDropdown(citySelect, response, 'Select City');
            citySelect.prop('disabled', false);
            console.log('Cities loaded successfully:', response.results ? response.results.length : response.length, 'cities');
        },
        error: function(xhr, status, error) {
            console.error('Error loading cities:', error, xhr.responseText);
            handleDropdownError(citySelect, 'Error loading cities');

            // Show user-friendly error message
            showErrorMessage('Unable to load cities. Please try again.');

            // Try fallback if available
            tryFallbackCities(stateId);
        }
    });
}

function tryFallbackStates(countryId) {
    $.ajax({
        url: '/lms/fix_ajax_routes.php',
        type: 'GET',
        data: { action: 'get-states', id: countryId },
        dataType: 'json',
        success: function(response) {
            const stateSelect = $('#state');
            populateDropdown(stateSelect, response, 'Select State');
            stateSelect.prop('disabled', false);
        },
        error: function() {
            handleDropdownError($('#state'), 'Unable to load states');
        }
    });
}

function tryFallbackCities(stateId) {
    $.ajax({
        url: '/lms/fix_ajax_routes.php',
        type: 'GET',
        data: { action: 'get-cities', id: stateId },
        dataType: 'json',
        success: function(response) {
            const citySelect = $('#city');
            populateDropdown(citySelect, response, 'Select City');
            citySelect.prop('disabled', false);
        },
        error: function() {
            handleDropdownError($('#city'), 'Unable to load cities');
        }
    });
}

function populateDropdown(selectElement, response, placeholder) {
    selectElement.empty();
    selectElement.append(`<option value="">${placeholder}</option>`);

    // Handle both Select2 format (with 'results') and direct array format
    const data = response.results || response;

    if (data && data.length > 0) {
        data.forEach(function(item) {
            // Handle both 'text' and 'name' properties
            const displayName = item.text || item.name;
            selectElement.append(`<option value="${item.id}">${displayName}</option>`);
        });
    }
}

function handleDropdownError(selectElement, message) {
    selectElement.prop('disabled', false)
        .empty()
        .append(`<option value="">${message}</option>`);
}

function clearDropdowns(dropdownIds) {
    dropdownIds.forEach(function(id) {
        const placeholder = id === 'state' ? 'Select State' : 'Select City';
        $(`#${id}`).empty().append(`<option value="">${placeholder}</option>`);
    });
}

function submitProfileForm() {
    const form = $('#profile-completion-form');
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.text();

    // Clear previous validation errors
    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');

    // Validate dependent dropdowns
    if (!validateDependentDropdowns()) {
        showErrorMessage('Please ensure country, state, and city selections are valid.');
        return;
    }

    // Show loading state
    submitBtn.prop('disabled', true).text('Updating Profile...');

    // Prepare form data
    const formData = new FormData(form[0]);

    // Make AJAX request
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuccessMessage('Profile updated successfully!');
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = response.redirect || '/home';
                }, 1500);
            } else {
                showErrorMessage(response.message || 'An error occurred. Please try again.');
                resetSubmitButton(submitBtn, originalText);
            }
        },
        error: function(xhr) {
            resetSubmitButton(submitBtn, originalText);

            if (xhr.status === 422) {
                // Handle validation errors
                const errors = xhr.responseJSON.errors;
                displayValidationErrors(errors);
            } else {
                console.error('Profile update error:', xhr.responseText);
                showErrorMessage('An error occurred. Please try again.');
            }
        }
    });
}

function resetSubmitButton(button, originalText) {
    button.prop('disabled', false).text(originalText);
}

function showSuccessMessage(message) {
    // Remove existing alerts
    $('.alert').remove();

    // Add success alert
    const alert = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $('#profileForm').prepend(alert);

    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function showErrorMessage(message) {
    // Remove existing alerts
    $('.alert').remove();

    // Add error alert
    const alert = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    $('#profileForm').prepend(alert);

    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function displayValidationErrors(errors) {
    // Clear previous error messages
    $('.invalid-feedback').remove();
    $('.is-invalid').removeClass('is-invalid');

    // Display new errors
    Object.keys(errors).forEach(function(field) {
        const input = $(`[name="${field}"]`);
        const errorMessage = errors[field][0];

        input.addClass('is-invalid');
        input.after(`<div class="invalid-feedback">${errorMessage}</div>`);
    });

    // Show general error message
    showErrorMessage('Please correct the errors below and try again.');
}

function validateDependentDropdowns() {
    const country = $('#country').val();
    const state = $('#state').val();
    const city = $('#city').val();

    // If country is selected, state must be selected
    if (country && !state) {
        $('#state').addClass('is-invalid');
        $('#state').after('<div class="invalid-feedback">Please select a state for the chosen country.</div>');
        return false;
    }

    // If state is selected, city must be selected
    if (state && !city) {
        $('#city').addClass('is-invalid');
        $('#city').after('<div class="invalid-feedback">Please select a city for the chosen state.</div>');
        return false;
    }

    // If city is selected, both country and state must be selected
    if (city && (!country || !state)) {
        if (!country) {
            $('#country').addClass('is-invalid');
            $('#country').after('<div class="invalid-feedback">Please select a country.</div>');
        }
        if (!state) {
            $('#state').addClass('is-invalid');
            $('#state').after('<div class="invalid-feedback">Please select a state.</div>');
        }
        return false;
    }

    return true;
}
