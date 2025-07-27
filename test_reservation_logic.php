<?php

// Test the reservation logic
require_once 'vendor/autoload.php';

// Create a simple test of the logic
echo "Testing reservation button disable logic:\n\n";

// Test scenario 1: File with active reservation
echo "Scenario 1: File with active reservation\n";
$hasActiveReservation = true;
$hasOngoingReservationLoan = false;
$file_borrow_status = 'Dipinjam';

if ($file_borrow_status === 'Dipinjam') {
    if ($hasActiveReservation) {
        echo "Result: Button disabled - 'Sudah Ditempah'\n";
    } elseif ($hasOngoingReservationLoan) {
        echo "Result: Button disabled - 'Tempah Tidak Tersedia'\n";
    } else {
        echo "Result: Button enabled - 'Tempah Fail'\n";
    }
} else {
    echo "Result: Show 'Mohon' button\n";
}

echo "\n";

// Test scenario 2: File with ongoing reservation loan
echo "Scenario 2: File with ongoing reservation loan\n";
$hasActiveReservation = false;
$hasOngoingReservationLoan = true;
$file_borrow_status = 'Dipinjam';

if ($file_borrow_status === 'Dipinjam') {
    if ($hasActiveReservation) {
        echo "Result: Button disabled - 'Sudah Ditempah'\n";
    } elseif ($hasOngoingReservationLoan) {
        echo "Result: Button disabled - 'Tempah Tidak Tersedia'\n";
    } else {
        echo "Result: Button enabled - 'Tempah Fail'\n";
    }
} else {
    echo "Result: Show 'Mohon' button\n";
}

echo "\n";

// Test scenario 3: File available for reservation
echo "Scenario 3: File available for reservation\n";
$hasActiveReservation = false;
$hasOngoingReservationLoan = false;
$file_borrow_status = 'Dipinjam';

if ($file_borrow_status === 'Dipinjam') {
    if ($hasActiveReservation) {
        echo "Result: Button disabled - 'Sudah Ditempah'\n";
    } elseif ($hasOngoingReservationLoan) {
        echo "Result: Button disabled - 'Tempah Tidak Tersedia'\n";
    } else {
        echo "Result: Button enabled - 'Tempah Fail'\n";
    }
} else {
    echo "Result: Show 'Mohon' button\n";
}

echo "\n";

// Test scenario 4: File not borrowed
echo "Scenario 4: File not borrowed\n";
$hasActiveReservation = false;
$hasOngoingReservationLoan = false;
$file_borrow_status = 'Tersedia';

if ($file_borrow_status === 'Dipinjam') {
    if ($hasActiveReservation) {
        echo "Result: Button disabled - 'Sudah Ditempah'\n";
    } elseif ($hasOngoingReservationLoan) {
        echo "Result: Button disabled - 'Tempah Tidak Tersedia'\n";
    } else {
        echo "Result: Button enabled - 'Tempah Fail'\n";
    }
} else {
    echo "Result: Show 'Mohon' button\n";
}

echo "\nLogic test completed successfully!\n";
