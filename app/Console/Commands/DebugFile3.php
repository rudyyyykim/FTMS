<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;

class DebugFile3 extends Command
{
    protected $signature = 'debug:file3';
    protected $description = 'Debug File ID 3 specifically';

    public function handle()
    {
        $file = File::with(['fileRequests.fileReturn'])->find(3);
        
        if (!$file) {
            $this->error('File not found');
            return;
        }
        
        $this->info("=== Debugging File ID 3: {$file->fileName} ===");
        $this->info("Current borrow_status: " . $file->borrow_status);
        $this->newLine();
        
        // Step 1: Check active requests with "Belum Dipulangkan"
        $activeRequests = $file->fileRequests()
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->get();
            
        $this->info("Step 1: Active requests with 'Belum Dipulangkan': " . $activeRequests->count());
        foreach ($activeRequests as $req) {
            $this->line("  Request {$req->requestID}: Reservation={$req->isReservation}, Status={$req->fileReturn->returnStatus}");
        }
        $this->newLine();
        
        // Step 2: Check requests without return record (excluding cancelled reservations)
        $requestsWithoutReturn = $file->fileRequests()
            ->whereDoesntHave('fileReturn')
            ->where(function($query) {
                $query->where('isReservation', false)  // Regular requests without returns
                      ->orWhere(function($subQuery) {
                          // Or reservations that are not cancelled
                          $subQuery->where('isReservation', true)
                                   ->where('reserveStatus', '!=', 'Dibatalkan');
                      });
            })
            ->get();
            
        $this->info("Step 2: Requests without return record: " . $requestsWithoutReturn->count());
        foreach ($requestsWithoutReturn as $req) {
            $this->line("  Request {$req->requestID}: Reservation={$req->isReservation}");
        }
        $this->newLine();
        
        // Step 3: Check reservations in process
        $reservationsInProcess = $file->fileRequests()
            ->where('isReservation', true)
            ->where('reserveStatus', 'Dalam Proses')
            ->get();
            
        $this->info("Step 3: Reservations 'Dalam Proses': " . $reservationsInProcess->count());
        foreach ($reservationsInProcess as $req) {
            $this->line("  Request {$req->requestID}: Status={$req->reserveStatus}");
        }
        $this->newLine();
        
        // Step 4: Check successful reservations not returned
        $successfulUnreturned = $file->fileRequests()
            ->where('isReservation', true)
            ->where('reserveStatus', 'Berjaya')
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->get();
            
        $this->info("Step 4: Successful reservations not returned: " . $successfulUnreturned->count());
        foreach ($successfulUnreturned as $req) {
            $this->line("  Request {$req->requestID}: Status={$req->reserveStatus}, Return={$req->fileReturn->returnStatus}");
        }
        $this->newLine();
        
        // Show all requests for reference
        $this->info("All requests for this file:");
        foreach ($file->fileRequests as $req) {
            $this->line("  Request {$req->requestID}:");
            $this->line("    isReservation: " . ($req->isReservation ? 'true' : 'false'));
            $this->line("    reserveStatus: {$req->reserveStatus}");
            if ($req->fileReturn) {
                $this->line("    returnStatus: {$req->fileReturn->returnStatus}");
                $this->line("    returnDate: {$req->fileReturn->returnDate}");
            } else {
                $this->line("    No return record");
            }
        }
        
        return 0;
    }
}
