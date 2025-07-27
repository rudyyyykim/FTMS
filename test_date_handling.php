<?php

// Test the date handling logic
require_once 'vendor/autoload.php';
use Carbon\Carbon;

echo "Testing date handling logic:\n\n";

// Helper functions
function formatDate($date) {
    if (is_string($date)) {
        return Carbon::parse($date)->format('d/m/Y');
    } elseif ($date instanceof Carbon || $date instanceof DateTime) {
        return $date->format('d/m/Y');
    }
    return $date;
}

function getCarbonDate($date) {
    if (is_string($date)) {
        return Carbon::parse($date);
    } elseif ($date instanceof Carbon) {
        return $date;
    } elseif ($date instanceof DateTime) {
        return Carbon::instance($date);
    }
    return Carbon::now();
}

// Test scenario 1: String date
echo "Scenario 1: String date\n";
$stringDate = '2025-07-30';
echo "Input: " . $stringDate . "\n";
echo "Formatted: " . formatDate($stringDate) . "\n";
echo "Carbon date: " . getCarbonDate($stringDate)->format('Y-m-d') . "\n";
echo "Min reservation date: " . getCarbonDate($stringDate)->copy()->addDay()->format('Y-m-d') . "\n\n";

// Test scenario 2: Carbon date
echo "Scenario 2: Carbon date\n";
$carbonDate = Carbon::parse('2025-07-30');
echo "Input: Carbon instance\n";
echo "Formatted: " . formatDate($carbonDate) . "\n";
echo "Carbon date: " . getCarbonDate($carbonDate)->format('Y-m-d') . "\n";
echo "Min reservation date: " . getCarbonDate($carbonDate)->copy()->addDay()->format('Y-m-d') . "\n\n";

// Test scenario 3: DateTime object
echo "Scenario 3: DateTime object\n";
$dateTime = new DateTime('2025-07-30');
echo "Input: DateTime instance\n";
echo "Formatted: " . formatDate($dateTime) . "\n";
echo "Carbon date: " . getCarbonDate($dateTime)->format('Y-m-d') . "\n";
echo "Min reservation date: " . getCarbonDate($dateTime)->copy()->addDay()->format('Y-m-d') . "\n\n";

echo "Date handling test completed successfully!\n";
