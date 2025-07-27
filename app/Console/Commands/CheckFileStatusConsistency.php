<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;

class CheckFileStatusConsistency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:file-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for file status inconsistencies between computed status and actual database state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== File Status Consistency Check ===');
        $this->info('Checking for inconsistencies...');
        $this->newLine();

        $totalIssues = 0;
        $files = File::all();

        foreach ($files as $file) {
            // Get all requests for this file
            $totalRequests = FileRequest::where('fileID', $file->fileID)->count();
            
            // Get requests without corresponding return records
            $requestsWithoutReturn = FileRequest::where('fileID', $file->fileID)
                ->whereNotExists(function ($query) {
                    $query->select('*')
                          ->from('fileReturn')
                          ->whereColumn('fileReturn.requestID', 'fileRequest.requestID');
                })->get();
            
            // Get returned requests that should be closed
            $returnedRequests = FileRequest::where('fileID', $file->fileID)
                ->whereExists(function ($query) {
                    $query->select('*')
                          ->from('fileReturn')
                          ->whereColumn('fileReturn.requestID', 'fileRequest.requestID')
                          ->where('fileReturn.returnStatus', 'Dipulangkan');
                })->get();
            
            $computedStatus = $file->borrow_status;
            
            // Check for issues
            $hasIssues = false;
            
            // Issue 1: File shows as available but has unreturned requests
            if ($computedStatus === 'Tersedia' && $requestsWithoutReturn->count() > 0) {
                $hasIssues = true;
                $this->error("ISSUE 1 - File ID: {$file->fileID} - {$file->tajuk_fail}");
                $this->error("Status shows as 'Tersedia' but has {$requestsWithoutReturn->count()} unreturned requests:");
                foreach ($requestsWithoutReturn as $req) {
                    $this->line("  - Request ID: {$req->requestID} by {$req->requestedBy} on {$req->requestDate}");
                }
                $this->newLine();
            }
            
            // Issue 2: File shows as borrowed but all requests are returned
            if ($computedStatus === 'Dipinjam' && $requestsWithoutReturn->count() === 0) {
                $hasIssues = true;
                $this->error("ISSUE 2 - File ID: {$file->fileID} - {$file->tajuk_fail}");
                $this->error("Status shows as 'Dipinjam' but all requests have been returned:");
                foreach ($returnedRequests as $req) {
                    $return = FileReturn::where('requestID', $req->requestID)->first();
                    $this->line("  - Request ID: {$req->requestID} returned on {$return->returnDate}");
                }
                $this->newLine();
            }
            
            if ($hasIssues) {
                $totalIssues++;
            }
        }

        $this->info('=== Summary ===');
        $this->info("Total files checked: " . $files->count());
        $this->info("Files with issues: $totalIssues");

        if ($totalIssues === 0) {
            $this->info("No issues found! All file statuses are consistent.");
        }

        $this->newLine();
        $this->info('=== Additional Debug Info ===');
        
        // Check for specific problematic files that user mentioned
        $problematicFiles = FileRequest::whereNotExists(function ($query) {
            $query->select('*')
                  ->from('fileReturn')
                  ->whereColumn('fileReturn.requestID', 'fileRequest.requestID');
        })->with('file')->get();

        if ($problematicFiles->count() > 0) {
            $this->info("Files that should show as 'Dipinjam' (have unreturned requests):");
            foreach ($problematicFiles as $req) {
                $this->line("- File ID: {$req->fileID} - {$req->file->tajuk_fail} (Request ID: {$req->requestID})");
                $this->line("  Computed status: {$req->file->borrow_status}");
            }
        } else {
            $this->info("All requests have corresponding return records.");
        }

        return 0;
    }
}
