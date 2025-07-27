<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if table exists
    $tableExists = DB::select("SHOW TABLES LIKE 'fileReturn'");
    if (empty($tableExists)) {
        echo "ERROR: fileReturn table does not exist!\n";
        exit;
    }
    
    echo "FileReturn table exists. Columns:\n";
    $columns = DB::select("DESCRIBE fileReturn");
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nTesting if lateReturn column exists:\n";
    $result = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileReturn' AND COLUMN_NAME = 'lateReturn'");
    if (empty($result)) {
        echo "ERROR: lateReturn column does NOT exist!\n";
    } else {
        echo "SUCCESS: lateReturn column exists\n";
    }
    
} catch (Exception $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
