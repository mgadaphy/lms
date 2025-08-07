<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== Database Table Check ===\n";
    
    // Check if states table exists
    if (Schema::hasTable('states')) {
        $statesCount = DB::table('states')->count();
        echo "✓ 'states' table exists with {$statesCount} records\n";
        
        // Show sample state for country 38 (Cameroon)
        $sampleStates = DB::table('states')
            ->where('country_id', 38)
            ->limit(3)
            ->get(['id', 'name', 'country_id']);
        
        if ($sampleStates->count() > 0) {
            echo "Sample states for country 38:\n";
            foreach ($sampleStates as $state) {
                echo "  - ID: {$state->id}, Name: {$state->name}\n";
            }
        } else {
            echo "No states found for country 38\n";
        }
    } else {
        echo "✗ 'states' table does not exist\n";
    }
    
    echo "\n";
    
    // Check if cities table exists
    if (Schema::hasTable('cities')) {
        $citiesCount = DB::table('cities')->count();
        echo "✓ 'cities' table exists with {$citiesCount} records\n";
    } else {
        echo "✗ 'cities' table does not exist\n";
    }
    
    // Check if spn_cities table exists
    if (Schema::hasTable('spn_cities')) {
        $spnCitiesCount = DB::table('spn_cities')->count();
        echo "✓ 'spn_cities' table exists with {$spnCitiesCount} records\n";
        
        // Show sample cities for first state we found
        if (isset($sampleStates) && $sampleStates->count() > 0) {
            $firstStateId = $sampleStates->first()->id;
            $sampleCities = DB::table('spn_cities')
                ->where('state_id', $firstStateId)
                ->limit(3)
                ->get(['id', 'name', 'state_id']);
            
            if ($sampleCities->count() > 0) {
                echo "Sample cities for state {$firstStateId}:\n";
                foreach ($sampleCities as $city) {
                    echo "  - ID: {$city->id}, Name: {$city->name}\n";
                }
            } else {
                echo "No cities found for state {$firstStateId}\n";
            }
        }
    } else {
        echo "✗ 'spn_cities' table does not exist\n";
    }
    
    echo "\n";
    
    // Check countries table
    if (Schema::hasTable('countries')) {
        $countriesCount = DB::table('countries')->count();
        echo "✓ 'countries' table exists with {$countriesCount} records\n";
        
        // Find Cameroon
        $cameroon = DB::table('countries')
            ->where('id', 38)
            ->orWhere('name', 'like', '%Cameroon%')
            ->first(['id', 'name']);
        
        if ($cameroon) {
            echo "Found Cameroon: ID {$cameroon->id}, Name: {$cameroon->name}\n";
        } else {
            echo "Cameroon not found in countries table\n";
        }
    } else {
        echo "✗ 'countries' table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== End Check ===\n";
?>