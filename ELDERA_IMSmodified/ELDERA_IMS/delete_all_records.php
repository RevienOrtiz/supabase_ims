<?php

require_once 'vendor/autoload.php';

use App\Models\Senior;
use App\Models\Application;
use App\Models\PensionApplication;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Deleting all records from database...\n";

try {
    // Delete records in reverse order of dependencies
    echo "Deleting pension applications...\n";
    $pensionCount = PensionApplication::count();
    PensionApplication::query()->delete();
    echo "Deleted {$pensionCount} pension applications.\n";
    
    echo "Deleting benefits applications...\n";
    $benefitsCount = \App\Models\BenefitsApplication::count();
    \App\Models\BenefitsApplication::query()->delete();
    echo "Deleted {$benefitsCount} benefits applications.\n";
    
    echo "Deleting applications...\n";
    $applicationCount = Application::count();
    Application::query()->delete();
    echo "Deleted {$applicationCount} applications.\n";
    
    echo "Deleting seniors...\n";
    $seniorCount = Senior::count();
    Senior::query()->delete();
    echo "Deleted {$seniorCount} seniors.\n";
    
    // Reset auto-increment counters
    echo "Resetting auto-increment counters...\n";
    $driver = \DB::getDriverName();
    if ($driver === 'mysql') {
        \DB::statement('ALTER TABLE pension_applications AUTO_INCREMENT = 1');
        \DB::statement('ALTER TABLE benefits_applications AUTO_INCREMENT = 1');
        \DB::statement('ALTER TABLE applications AUTO_INCREMENT = 1');
        \DB::statement('ALTER TABLE seniors AUTO_INCREMENT = 1');
    } else {
        foreach (['pension_applications', 'benefits_applications', 'applications', 'seniors'] as $table) {
            $row = \DB::selectOne("SELECT pg_get_serial_sequence(?, 'id') AS seq", [$table]);
            if ($row && isset($row->seq) && $row->seq) {
                \DB::statement("ALTER SEQUENCE {$row->seq} RESTART WITH 1");
            }
        }
    }
    
    echo "\nDatabase cleanup completed successfully!\n";
    echo "All records have been deleted and counters reset.\n";
    
} catch (Exception $e) {
    echo "Error during deletion: " . $e->getMessage() . "\n";
}
