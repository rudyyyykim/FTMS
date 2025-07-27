<?php

// Debug script to test password change manually
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Get application instance
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Manual Password Change Test ===\n\n";

// Find a test user
$user = User::where('email', 'rudyykim@gmail.com')->first();

if (!$user) {
    echo "Test user not found!\n";
    exit;
}

echo "Testing with user: {$user->email}\n";
echo "Current password hash: " . substr($user->password, 0, 50) . "...\n\n";

// Test setting a new password using the model's mutator
$newPassword = 'newtest123';
echo "Setting new password: {$newPassword}\n";

// This should use the model's setPasswordAttribute mutator
$user->password = $newPassword;
$user->save();

echo "Password updated!\n";
echo "New password hash: " . substr($user->password, 0, 50) . "...\n\n";

// Test verification
$isValid = Hash::check($newPassword, $user->password);
echo "Verification test: " . ($isValid ? "✓ SUCCESS - Password works!" : "✗ FAILED - Password not working!") . "\n\n";

// Test with wrong password
$wrongPassword = 'wrongpassword';
$isInvalid = Hash::check($wrongPassword, $user->password);
echo "Wrong password test: " . ($isInvalid ? "✗ FAILED - Wrong password accepted!" : "✓ SUCCESS - Wrong password rejected!") . "\n\n";

echo "=== Test Complete ===\n";
echo "You can now try logging in with:\n";
echo "Email: {$user->email}\n";
echo "Password: {$newPassword}\n";
