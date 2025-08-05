<?php
// Standalone Profile Completion Page - Bypasses broken Laravel
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get countries for dropdown
    $stmt = $pdo->query("SELECT id, name FROM countries ORDER BY name");
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #5D78FF 0%, #5D78FF 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .form-container {
            padding: 40px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #5D78FF;
            box-shadow: 0 0 0 3px rgba(93, 120, 255, 0.1);
        }
        .row {
            display: flex;
            gap: 20px;
        }
        .col-md-6 {
            flex: 1;
        }
        .btn-primary {
            background: linear-gradient(135deg, #5D78FF 0%, #5D78FF 100%);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(93, 120, 255, 0.3);
        }
        .select2-container--default .select2-selection--single {
            height: 48px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 15px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Complete Your Profile</h1>
            <p>Please provide the following information to complete your profile</p>
        </div>
        
        <div class="form-container">
            <div id="success-message" class="success-message"></div>
            <div id="error-message" class="error-message"></div>
            
            <form id="profile-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="gender">Gender <span class="required">*</span></label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   placeholder="Enter your phone number" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dob">Date of Birth <span class="required">*</span></label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address <span class="required">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" 
                                   placeholder="Enter your address" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country">Country <span class="required">*</span></label>
                            <select name="country" id="country" class="form-control" required>
                                <option value="">Select Country</option>
                                <?php foreach ($countries as $country): ?>
                                    <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="state">State/Province <span class="required">*</span></label>
                            <select name="state" id="state" class="form-control stateList" required>
                                <option value="">Select State/Province</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city">City <span class="required">*</span></label>
                            <select name="city" id="city" class="form-control cityList" required>
                                <option value="">Select City</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="about">About Me</label>
                            <textarea class="form-control" id="about" name="about" rows="3" 
                                      placeholder="Tell us about yourself..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <span id="submit-text">Save & Continue</span>
                        <span id="submit-spinner" style="display: none;">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for regular dropdowns (not state/city)
            $('#gender').select2({
                width: '100%'
            });

            // Initialize state select2 with AJAX
            $('.stateList').select2({
                width: '100%',
                ajax: {
                    url: '/ajax_states.php',
                    type: "GET",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var countryId = $('#country').find(':selected').val();
                        console.log('State AJAX request - Country ID:', countryId);
                        return {
                            search: params.term,
                            page: params.page || 1,
                            id: countryId,
                        }
                    },
                    success: function(data) {
                        console.log('State AJAX success:', data);
                    },
                    error: function(xhr, status, error) {
                        console.error('State AJAX error:', status, error);
                        console.error('Response:', xhr.responseText);
                    },
                    cache: false
                }
            });

            // Initialize city select2 with AJAX
            $('.cityList').select2({
                width: '100%',
                ajax: {
                    url: '/ajax_cities.php',
                    type: "GET",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var stateId = $('#state').find(':selected').val();
                        console.log('City AJAX request - State ID:', stateId);
                        return {
                            search: params.term,
                            page: params.page || 1,
                            id: stateId,
                        }
                    },
                    success: function(data) {
                        console.log('City AJAX success:', data);
                    },
                    error: function(xhr, status, error) {
                        console.error('City AJAX error:', status, error);
                        console.error('Response:', xhr.responseText);
                    },
                    cache: false
                }
            });

            // Country change event
            $('#country').on('change', function() {
                var countryId = $(this).val();
                var countryName = $(this).find(':selected').text();
                console.log('Country changed to:', countryId, countryName);
                
                // Clear state and city
                $('.stateList').val(null).trigger('change');
                $('.cityList').val(null).trigger('change');
                
                if (countryId) {
                    console.log('Country selected - should trigger state loading');
                }
            });

            // State change event
            $('#state').on('change', function() {
                var stateId = $(this).val();
                var stateName = $(this).find(':selected').text();
                console.log('State changed to:', stateId, stateName);
                
                // Clear city
                $('.cityList').val(null).trigger('change');
                
                if (stateId) {
                    console.log('State selected - should trigger city loading');
                }
            });

            // Form submission
            $('#profile-form').on('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = $('.btn-primary');
                const submitText = $('#submit-text');
                const submitSpinner = $('#submit-spinner');
                
                submitBtn.prop('disabled', true);
                submitText.hide();
                submitSpinner.show();
                
                // Get form data
                const formData = new FormData(this);
                
                // Submit to standalone handler
                $.ajax({
                    url: '/standalone_profile_save.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#success-message').text(response.message).show();
                            $('#error-message').hide();
                        } else {
                            $('#error-message').text(response.message).show();
                            $('#success-message').hide();
                        }
                    },
                    error: function() {
                        $('#error-message').text('An error occurred. Please try again.').show();
                        $('#success-message').hide();
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitText.show();
                        submitSpinner.hide();
                    }
                });
            });
        });
    </script>
</body>
</html> 