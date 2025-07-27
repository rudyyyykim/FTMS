<?php

// Test the reservation date logic
echo "Testing reservation date restriction logic:\n\n";

// Simulate Carbon date object
require_once 'vendor/autoload.php';
use Carbon\Carbon;

// Test scenario 1: Current borrower with return date 2025-07-30
echo "Scenario 1: Current borrower returns on 2025-07-30\n";
$returnDate = Carbon::parse('2025-07-30');
$minReservationDate = $returnDate->copy()->addDay()->format('Y-m-d');
echo "Return date: " . $returnDate->format('d/m/Y') . "\n";
echo "Minimum reservation date: " . Carbon::parse($minReservationDate)->format('d/m/Y') . "\n";
echo "HTML min attribute: " . $minReservationDate . "\n\n";

// Test scenario 2: No current borrower (file available)
echo "Scenario 2: No current borrower (file available)\n";
$minReservationDate = date('Y-m-d');
echo "Minimum reservation date: " . Carbon::parse($minReservationDate)->format('d/m/Y') . "\n";
echo "HTML min attribute: " . $minReservationDate . "\n\n";

// Test scenario 3: Current borrower returns today
echo "Scenario 3: Current borrower returns today\n";
$returnDate = Carbon::today();
$minReservationDate = $returnDate->copy()->addDay()->format('Y-m-d');
echo "Return date: " . $returnDate->format('d/m/Y') . "\n";
echo "Minimum reservation date: " . Carbon::parse($minReservationDate)->format('d/m/Y') . "\n";
echo "HTML min attribute: " . $minReservationDate . "\n\n";

echo "Logic test completed successfully!\n";
