<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\FileReturn;

try {
    $sample = FileReturn::first();
    if ($sample) {
        echo "FileReturn columns found:\n";
        print_r($sample->getAttributes());
    } else {
        echo "No FileReturn records found, checking table structure...\n";
        echo "Fillable fields: " . implode(', ', (new FileReturn())->getFillable()) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
