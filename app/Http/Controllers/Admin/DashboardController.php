<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        
        // Get total files count (always show all files)
        $totalFiles = File::count();
        
        // Get borrowed files count based on user role
        $borrowedFilesQuery = FileReturn::where('returnStatus', 'Belum Dipulangkan');
        
        // Filter by user role - PKA users can only see files they handle
        if ($currentUser && $currentUser->role === 'Pka') {
            $borrowedFilesQuery->where('userID', $currentUser->userID);
        }
        // Admin users can see all borrowed files (no additional filtering)
        
        $borrowedFiles = $borrowedFilesQuery->count();
        
        // Get total registered users count (always show all users)
        $totalUsers = User::count();
        
        // Get weekly file request data (Monday to Friday of current week)
        $weeklyData = $this->getWeeklyRequestData();
        
        // Get overdue files data (now filtered by user role)
        $allOverdueFiles = $this->getOverdueFiles();
        $overdueCount = count($allOverdueFiles);
        
        // Separate files by status
        $overdueFiles = $allOverdueFiles->where('status', 'overdue')->values();
        $dueTodayFiles = $allOverdueFiles->where('status', 'due_today')->values();

        return view('Admin.adminDashboard', compact('totalFiles', 'borrowedFiles', 'totalUsers', 'weeklyData', 'overdueFiles', 'dueTodayFiles', 'overdueCount', 'allOverdueFiles'));
    }
    
    private function getOverdueFiles()
    {
        $today = Carbon::today();
        $currentUser = auth()->user();
        
        // Get files that are overdue OR due today (return date <= today and status is still "Belum Dipulangkan")
        $query = FileReturn::where('returnStatus', 'Belum Dipulangkan')
                          ->where('returnDate', '<=', $today)
                          ->with(['fileRequest.file', 'fileRequest.staff']);
        
        // Filter by user role - PKA users can only see files they handle
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        // Admin users can see all overdue files (no additional filtering)
        
        $overdueFiles = $query->get()
                             ->map(function($fileReturn) use ($today) {
                                 $file = $fileReturn->fileRequest->file;
                                 $staff = $fileReturn->fileRequest->staff;
                                 $returnDate = Carbon::parse($fileReturn->returnDate);
                                 
                                 $fileCodeDisplay = $file ? 
                                     "{$file->functionCode} - {$file->activityCode}/{$file->subActivityCode}/{$file->fileCode} - {$file->fileName}" 
                                     : 'Unknown File';
                                 
                                 // Format phone number for WhatsApp (Malaysian format)
                                 $staffPhone = $staff->staffPhone ?? '';
                                 $whatsappPhone = '';
                                 if ($staffPhone) {
                                     // Remove all non-numeric characters
                                     $whatsappPhone = preg_replace('/[^0-9]/', '', $staffPhone);
                                     // Convert Malaysian numbers: replace leading 0 with 60
                                     if (substr($whatsappPhone, 0, 1) === '0') {
                                         $whatsappPhone = '60' . substr($whatsappPhone, 1);
                                     }
                                 }
                                 
                                 // Determine status: due today or overdue
                                 $status = $returnDate->isToday() ? 'due_today' : 'overdue';
                                 $daysOverdue = $returnDate->diffInDays($today, false);
                                 
                                 return [
                                     'fileName' => $file->fileName ?? 'Unknown File',
                                     'fileCodeDisplay' => $fileCodeDisplay,
                                     'staffName' => $staff->staffName ?? 'Unknown Staff',
                                     'staffPhone' => $whatsappPhone,
                                     'returnDate' => $returnDate,
                                     'daysOverdue' => abs($daysOverdue),
                                         'status' => $status
                                     ];
                                 });

        return $overdueFiles;
    }    private function getWeeklyRequestData()
    {
        // Get the start (Monday) and end (Friday) of current week
        $startOfWeek = now()->startOfWeek(); // Monday
        $endOfWeek = now()->startOfWeek()->addDays(4); // Friday
        
        // Initialize data array for each day
        $weeklyData = [
            'Monday' => 0,
            'Tuesday' => 0,
            'Wednesday' => 0,
            'Thursday' => 0,
            'Friday' => 0
        ];
        
        // Get file requests for current week (excluding cancelled reservations)
        $requests = FileRequest::whereBetween('requestDate', [$startOfWeek, $endOfWeek])
                              ->where('reserveStatus', '!=', FileRequest::STATUS_DIBATALKAN)
                              ->selectRaw('DATE(requestDate) as request_date, COUNT(*) as count')
                              ->groupBy('request_date')
                              ->get();
        
        // Map the results to days of the week
        foreach ($requests as $request) {
            $dayOfWeek = \Carbon\Carbon::parse($request->request_date)->format('l'); // Get day name
            if (array_key_exists($dayOfWeek, $weeklyData)) {
                $weeklyData[$dayOfWeek] = $request->count;
            }
        }
        
        return array_values($weeklyData); // Return just the values for the chart
    }
}