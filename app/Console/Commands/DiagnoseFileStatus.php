<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;

class DiagnoseFileStatus extends Command
{
    protected $signature = 'diagnose:file-status';
    protected $description = 'Diagnose file status logic and identify inconsistencies';

    public function handle()
    {
        $this->info('=== Diagnosing File Status Logic ===');
        
        // Get files that might have issues
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
                $this->error("ISSUE - File ID: {$file->fileID} - {$file->fileName}");
                $this->line("  Computed Status: {$computedStatus}");
                $this->line("  Expected Status: {$expectedStatus}");
                $this->line("  Total Requests: " . $file->fileRequests->count());
                $this->line("  Requests with 'Belum Dipulangkan': " . $requestsWithUnreturnedStatus->count());
                $this->line("  Requests without return record: " . $requestsWithoutReturn->count());
                
                foreach ($file->fileRequests as $request) {
                    $this->line("    Request ID: {$request->requestID}");
                    if ($request->fileReturn) {
                        $this->line("      Return Status: {$request->fileReturn->returnStatus}");
                        $this->line("      Return Date: {$request->fileReturn->returnDate}");
                    } else {
                        $this->line("      No return record");
                    }
                }
                $this->newLine();
            }
        }

        $this->info("=== Summary ===");
        $this->info("Total files checked: " . $files->count());
        $this->info("Files with status issues: {$issueCount}");

        if ($issueCount === 0) {
            $this->info("No status logic issues found.");
        }

        // Check for files that should be Tersedia but show as Dipinjam
        $this->newLine();
        $this->info("=== Files with All Returned Requests ===");
        
        $problematicFiles = File::whereHas('fileRequests')->get()->filter(function($file) {
            // Check if all requests for this file have been returned
            $allRequestsReturned = $file->fileRequests->every(function($request) {
                if ($request->isReservation) {
                    // For reservations, check if they are completed/cancelled or successfully returned
                    return $request->reserveStatus === 'Dibatalkan' || 
                           ($request->reserveStatus === 'Berjaya' && 
                            $request->fileReturn && 
                            $request->fileReturn->returnStatus === 'Dipulangkan');
                } else {
                    // For regular requests, check if they have been returned
                    return $request->fileReturn && $request->fileReturn->returnStatus === 'Dipulangkan';
                }
            });
            
            return $allRequestsReturned && $file->borrow_status === 'Dipinjam';
        });

        if ($problematicFiles->count() > 0) {
            $this->error("Found files that should be 'Tersedia' but show as 'Dipinjam':");
            foreach ($problematicFiles as $file) {
                $this->line("- File ID: {$file->fileID} - {$file->fileName}");
                foreach ($file->fileRequests as $request) {
                    if ($request->isReservation) {
                        $this->line("  Reservation {$request->requestID}: {$request->reserveStatus}");
                        if ($request->fileReturn) {
                            $this->line("    Return Status: {$request->fileReturn->returnStatus} ({$request->fileReturn->returnDate})");
                        }
                    } else {
                        $this->line("  Request {$request->requestID}: " . ($request->fileReturn ? $request->fileReturn->returnStatus . " ({$request->fileReturn->returnDate})" : "No return record"));
                    }
                }
            }
        } else {
            $this->info("No files found with all returned requests showing as Dipinjam.");
        }

        // Additional check for reservation-specific issues
        $this->newLine();
        $this->info("=== Reservation Status Check ===");
        
        $reservationFiles = File::whereHas('fileRequests', function($query) {
            $query->where('isReservation', true);
        })->get();
        
        $reservationIssues = 0;
        foreach ($reservationFiles as $file) {
            $reservations = $file->fileRequests->where('isReservation', true);
            $status = $file->borrow_status;
            
            $activeReservations = $reservations->filter(function($res) {
                return $res->reserveStatus === 'Dalam Proses' || 
                       ($res->reserveStatus === 'Berjaya' && 
                        $res->fileReturn && 
                        $res->fileReturn->returnStatus === 'Belum Dipulangkan');
            });
            
            $shouldBeDipinjam = $activeReservations->count() > 0;
            $expectedStatus = $shouldBeDipinjam ? 'Dipinjam' : 'Tersedia';
            
            // Only check files that only have reservations (no regular requests)
            $hasOnlyReservations = $file->fileRequests->every(function($req) {
                return $req->isReservation;
            });
            
            if ($hasOnlyReservations && $status !== $expectedStatus) {
                $reservationIssues++;
                $this->error("Reservation Issue - File ID: {$file->fileID}");
                $this->line("  Current Status: {$status}");
                $this->line("  Expected Status: {$expectedStatus}");
                $this->line("  Active Reservations: " . $activeReservations->count());
                foreach ($reservations as $res) {
                    $this->line("    Reservation {$res->requestID}: {$res->reserveStatus}");
                    if ($res->fileReturn) {
                        $this->line("      Return: {$res->fileReturn->returnStatus}");
                    }
                }
            }
        }
        
        $this->info("Reservation files checked: " . $reservationFiles->count());
        $this->info("Reservation issues found: {$reservationIssues}");

        return 0;
    }
}
