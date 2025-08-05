<?php
// Simple database test
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Testing database connection...\n";
    
    // Check if states table exists
    $tables = DB::select("SHOW TABLES LIKE 'states'");
    echo "States table exists: " . (count($tables) > 0 ? 'YES' : 'NO') . "\n";
    
    if (count($tables) > 0) {
        // Check total states
        $totalStates = DB::table('states')->count();
        echo "Total states in database: " . $totalStates . "\n";
        
        // Check states for country ID 1 (Cameroon)
        $cameroonStates = DB::table('states')->where('country_id', 1)->get();
        echo "States for Cameroon (country_id=1): " . count($cameroonStates) . "\n";
        
        if (count($cameroonStates) > 0) {
            echo "Sample Cameroon states:\n";
            foreach ($cameroonStates->take(5) as $state) {
                echo "- ID: " . $state->id . ", Name: " . $state->name . "\n";
            }
        }
        
        // Check countries table
        $countries = DB::table('countries')->where('id', 1)->first();
        if ($countries) {
            echo "Country ID 1: " . $countries->name . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 