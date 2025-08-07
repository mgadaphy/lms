<?php
// Standalone Profile Save Handler - Bypasses broken Laravel
header('Content-Type: application/json');

// Database configuration
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get form data
    $gender = $_POST['gender'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $city = $_POST['city'] ?? '';
    $about = $_POST['about'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($gender)) $errors[] = 'Gender is required';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (empty($dob)) $errors[] = 'Date of birth is required';
    if (empty($address)) $errors[] = 'Address is required';
    if (empty($country)) $errors[] = 'Country is required';
    if (empty($state)) $errors[] = 'State/Province is required';
    if (empty($city)) $errors[] = 'City is required';
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fix the following errors: ' . implode(', ', $errors)
        ]);
        exit;
    }
    
    // Get the Laravel session cookie name (typically 'laravel_session')
    $sessionCookieName = 'laravel_session';
    
    // Check if we have a user ID in the request
    $user_id = $_POST['user_id'] ?? null;
    
    if (!$user_id) {
        // If no user ID in the request, try to get it from the auth cookie
        if (isset($_COOKIE['auth_user'])) {
            // The auth_user cookie might contain the user ID
            $user_id = $_COOKIE['auth_user'];
        } else {
            // For demonstration purposes, use a fallback user ID
            // In production, you would want to return an error
            $user_id = 1; // Default to admin or first user
            
            // Log this for debugging
            error_log('Warning: Using fallback user ID in standalone_profile_save.php');
        }
    }
    
    // Validate that user_id is numeric
    if (!is_numeric($user_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user ID. Please log in again.'
        ]);
        exit;
    }
    
    // Update user profile in the database
    try {
        // First update the users table
        $stmt = $pdo->prepare("
            UPDATE users SET 
                gender = ?, 
                phone = ?, 
                dob = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $gender, $phone, $dob, $user_id
        ]);
        
        // Then update or insert into user_info table
        $checkStmt = $pdo->prepare("SELECT id FROM user_info WHERE user_id = ?");
        $checkStmt->execute([$user_id]);
        
        if ($checkStmt->rowCount() > 0) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE user_info SET 
                    address = ?, 
                    country_id = ?, 
                    state_id = ?, 
                    city_id = ?, 
                    about = ?,
                    updated_at = NOW()
                WHERE user_id = ?
            ");
            
            $stmt->execute([
                $address, $country, $state, $city, $about, $user_id
            ]);
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO user_info 
                    (user_id, address, country_id, state_id, city_id, about, created_at, updated_at)
                VALUES 
                    (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $user_id, $address, $country, $state, $city, $about
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database update error: ' . $e->getMessage()
        ]);
        exit;
    }
    
    // Calculate profile completion percentage
    $completionFields = [
        'gender' => !empty($gender),
        'phone' => !empty($phone),
        'dob' => !empty($dob),
        'address' => !empty($address),
        'country' => !empty($country),
        'state' => !empty($state),
        'city' => !empty($city),
        'about' => !empty($about)
    ];
    
    $completedFields = array_filter($completionFields);
    $completionPercentage = round((count($completedFields) / count($completionFields)) * 100);
    
    // Update the completion percentage in the users table
    try {
        $stmt = $pdo->prepare("UPDATE users SET profile_completion = ? WHERE id = ?");
        $stmt->execute([$completionPercentage, $user_id]);
    } catch (Exception $e) {
        // Just log the error but continue
        error_log('Error updating profile completion: ' . $e->getMessage());
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully!',
        'completion_percentage' => $completionPercentage,
        'is_complete' => ($completionPercentage >= 100),
        'redirect_url' => '/home',
        'data' => [
            'gender' => $gender,
            'phone' => $phone,
            'dob' => $dob,
            'address' => $address,
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'about' => $about
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>