<?php
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== States Table Debug ===\n\n";

// Test 1: Check if states table exists
echo "1. Checking if states table exists...\n";
try {
    $exists = Schema::hasTable('states');
    echo "✓ States table exists: " . ($exists ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "✗ Error checking states table: " . $e->getMessage() . "\n";
}

// Test 2: Check if countries table exists
echo "\n2. Checking if countries table exists...\n";
try {
    $exists = Schema::hasTable('countries');
    echo "✓ Countries table exists: " . ($exists ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "✗ Error checking countries table: " . $e->getMessage() . "\n";
}

// Test 3: Check states table
echo "\n3. Checking states table...\n";
try {
    $result = DB::select("SELECT COUNT(*) as count FROM states");
    echo "✓ States table has " . $result[0]->count . " records\n";
    
    // Show first few states
    $states = DB::select("SELECT id, name, country_id FROM states LIMIT 5");
    echo "✓ First 5 states:\n";
    foreach ($states as $state) {
        echo "  - ID: {$state->id}, Name: {$state->name}, Country ID: {$state->country_id}\n";
    }
} catch (Exception $e) {
    echo "✗ States table error: " . $e->getMessage() . "\n";
}

// Test 4: Check countries table
echo "\n4. Checking countries table...\n";
try {
    $result = DB::select("SELECT COUNT(*) as count FROM countries");
    echo "✓ Countries table has " . $result[0]->count . " records\n";
    
    // Show first few countries
    $countries = DB::select("SELECT id, name FROM countries LIMIT 5");
    echo "✓ First 5 countries:\n";
    foreach ($countries as $country) {
        echo "  - ID: {$country->id}, Name: {$country->name}\n";
    }
} catch (Exception $e) {
    echo "✗ Countries table error: " . $e->getMessage() . "\n";
}

// Test 5: Test the AJAX query that should work
echo "\n5. Testing AJAX query for country ID 38 (assuming it exists)...\n";
try {
    $states = DB::table('states')
        ->select('id', 'name')
        ->where('name', 'like', '%' . '' . '%')
        ->where('country_id', '=', 38)
        ->get();
    
    echo "✓ Found " . $states->count() . " states for country ID 38:\n";
    foreach ($states as $state) {
        echo "  - ID: {$state->id}, Name: {$state->name}\n";
    }
} catch (Exception $e) {
    echo "✗ AJAX query error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n"; 