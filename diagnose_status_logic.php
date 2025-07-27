<?php

use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

echo "=== Checking File Status Logic ===\n";

// Get all files and check their status
$files = File::with(['fileRequests.fileReturn'])->get();

$issueCount = 0;

foreach ($files as $file) {
    $computedStatus = $file->borrow_status;
    
    // Manual check of the same logic
    $requestsWithUnreturnedStatus = $file->fileRequests->filter(function($request) {
        return $request->fileReturn && $request->fileReturn->returnStatus === 'Belum Dipulangkan';
    });
    
    $requestsWithoutReturn = $file->fileRequests->filter(function($request) {
        return !$request->fileReturn;
    });
    
    $shouldBeDipinjam = $requestsWithUnreturnedStatus->count() > 0 || $requestsWithoutReturn->count() > 0;
    $expectedStatus = $shouldBeDipinjam ? 'Dipinjam' : 'Tersedia';
    
    if ($computedStatus !== $expectedStatus) {
        $issueCount++;
        echo "ISSUE FOUND - File ID: {$file->fileID} - {$file->fileName}\n";
        echo "  Computed Status: {$computedStatus}\n";
        echo "  Expected Status: {$expectedStatus}\n";
        echo "  Total Requests: " . $file->fileRequests->count() . "\n";
        echo "  Requests with 'Belum Dipulangkan': " . $requestsWithUnreturnedStatus->count() . "\n";
        echo "  Requests without return record: " . $requestsWithoutReturn->count() . "\n";
        
        foreach ($file->fileRequests as $request) {
            echo "    Request ID: {$request->requestID}\n";
            if ($request->fileReturn) {
                echo "      Return Status: {$request->fileReturn->returnStatus}\n";
                echo "      Return Date: {$request->fileReturn->returnDate}\n";
            } else {
                echo "      No return record\n";
            }
        }
        echo "\n";
    }
}

echo "=== Summary ===\n";
echo "Total files checked: " . $files->count() . "\n";
echo "Files with status issues: {$issueCount}\n";

if ($issueCount === 0) {
    echo "No status logic issues found.\n";
    echo "The issue might be with data inconsistency or caching.\n";
}

// Additional check: Look for specific patterns that might cause issues
echo "\n=== Additional Checks ===\n";

// Check for files that have all requests returned but still show as Dipinjam
$potentialIssues = File::whereHas('fileRequests.fileReturn', function($query) {
    $query->where('returnStatus', 'Dipulangkan');
})->get();

echo "Files that have returned requests:\n";
foreach ($potentialIssues as $file) {
    $allRequestsReturned = $file->fileRequests->every(function($request) {
        return $request->fileReturn && $request->fileReturn->returnStatus === 'Dipulangkan';
    });
    
    if ($allRequestsReturned && $file->borrow_status === 'Dipinjam') {
        echo "  File ID: {$file->fileID} - All requests returned but status is Dipinjam\n";
        foreach ($file->fileRequests as $request) {
            echo "    Request {$request->requestID}: {$request->fileReturn->returnStatus}\n";
        }
    }
}

?>
