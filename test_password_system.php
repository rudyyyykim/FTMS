<?php

// Simple test script to check user passwords
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get application instance
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing User Password System ===\n\n";

// Test if we can find users
$users = User::take(3)->get();
echo "Found " . $users->count() . " users in database:\n";

foreach ($users as $user) {
    echo "- Email: {$user->email}, Role: {$user->role}, Status: {$user->userStatus}\n";
}

echo "\n=== Testing Password Verification ===\n";

if ($users->count() > 0) {
    $testUser = $users->first();
    echo "Testing with user: {$testUser->email}\n";
    echo "Current password hash: " . substr($testUser->password, 0, 50) . "...\n";
    echo "Hash length: " . strlen($testUser->password) . " characters\n";
    
    // Test some common passwords
    $testPasswords = ['password', '12345678', 'admin123', 'test123'];
    
    foreach ($testPasswords as $testPassword) {
        $isValid = Hash::check($testPassword, $testUser->password);
        echo "Password '{$testPassword}': " . ($isValid ? "✓ VALID" : "✗ Invalid") . "\n";
    }
} else {
    echo "No users found to test with.\n";
}

echo "\n=== Testing Hash Generation ===\n";
$testPassword = "test123";
$hash1 = Hash::make($testPassword);
$hash2 = Hash::make($testPassword);

echo "Test password: {$testPassword}\n";
echo "Hash 1: " . substr($hash1, 0, 50) . "...\n";
echo "Hash 2: " . substr($hash2, 0, 50) . "...\n";
echo "Hash 1 length: " . strlen($hash1) . "\n";
echo "Hash 2 length: " . strlen($hash2) . "\n";
echo "Verification test: " . (Hash::check($testPassword, $hash1) ? "✓ PASS" : "✗ FAIL") . "\n";
