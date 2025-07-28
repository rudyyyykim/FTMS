<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Staff;
use App\Models\FileRequest;
use App\Models\FileReturn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class ManageRequestController extends Controller
{
    public function index()
    {
        return view('Admin.manageRequest');
    }

    public function getRequestsData(Request $request)
    {
        $files = File::with(['fileRequests' => function($query) {
                $query->with('fileReturn');
            }])
            ->select(
                'files.fileID',
                'files.functionCode',
                'files.activityCode',
                'files.subActivityCode',
                'files.fileCode',
                'files.fileName',
                'files.fileDescription',
                'files.fileLevel',
                'files.fileStatus',
                'files.fileLocation'
            )
            ->get();

        return DataTables::of($files)
            ->addIndexColumn()
            ->addColumn('file_code', function ($file) {
                return $file->functionCode . '-' . $file->activityCode . '/' . $file->subActivityCode . '/' . $file->fileCode;
            })
            ->addColumn('file_name', function ($file) {
                return $file->fileName;
            })
            ->addColumn('borrow_status', function ($file) {
                $status = $file->borrow_status; // Using our accessor
                $badgeClass = $status === 'Dipinjam' ? 'badge-warning' : 'badge-success';
                return '<span class="badge '.$badgeClass.'">'.$status.'</span>';
            })
            ->addColumn('action', function ($file) {
                // Check if there's already an active reservation for this file
                $hasActiveReservation = FileRequest::where('fileID', $file->fileID)
                    ->where('isReservation', true)
                    ->where('reserveStatus', 'Dalam Proses')
                    ->exists();

                // Check if there's an ongoing loan from a reservation
                $hasOngoingReservationLoan = FileRequest::where('fileID', $file->fileID)
                    ->where('isReservation', true)
                    ->where('reserveStatus', 'Berjaya')
                    ->whereHas('fileReturn', function($query) {
                        $query->where('returnStatus', 'Belum Dipulangkan');
                    })
                    ->exists();

                // Show "Tempah Fail" button if status is "Dipinjam" and no existing reservation/ongoing reservation loan
                if ($file->borrow_status === 'Dipinjam') {
                    if ($hasActiveReservation) {
                        return '<button class="btn btn-secondary btn-sm" disabled title="Sudah ada tempahan aktif">Sudah Ditempah</button>';
                    } elseif ($hasOngoingReservationLoan) {
                        return '<button class="btn btn-secondary btn-sm" disabled title="Fail sedang dipinjam dari tempahan">Tempah Tidak Tersedia</button>';
                    } else {
                        return '<a href="' . route('admin.reserveFileForm', $file->fileID) . '" class="btn btn-warning btn-sm">Tempah Fail</a>';
                    }
                } else {
                    return '<a href="' . route('admin.requestFileForm', $file->fileID) . '" class="btn btn-sidebar btn-sm">Mohon</a>';
                }
            })
            ->rawColumns(['borrow_status', 'action'])
            ->make(true);
    }

    public function requestFileForm($fileID)
    {
        $file = File::findOrFail($fileID);
        return view('Admin.requestFileForm', compact('file'));
    }

    public function searchStaff(Request $request)
    {
        $search = $request->input('search');
        $fileID = $request->input('fileID'); // Get fileID to exclude current borrower
        
        // Get current borrowers if fileID is provided
        $excludeStaffIDs = [];
        if ($fileID) {
            $currentBorrowers = FileRequest::where('fileID', $fileID)
                ->whereHas('fileReturn', function($query) {
                    $query->where('returnStatus', 'Belum Dipulangkan');
                })
                ->pluck('staffID')
                ->toArray();
            $excludeStaffIDs = $currentBorrowers;
        }
        
        $staff = Staff::where(function($query) use ($search) {
                $query->where('staffName', 'like', "%$search%")
                    ->orWhere('staffEmail', 'like', "%$search%")
                    ->orWhere('staffPhone', 'like', "%$search%");
            })
            ->when(!empty($excludeStaffIDs), function($query) use ($excludeStaffIDs) {
                $query->whereNotIn('staffID', $excludeStaffIDs);
            })
            ->orderBy('staffName') // Order by name for better UX
            ->limit(10) // Limit to 10 results
            ->get();
            
        return response()->json($staff);
    }

    public function submitFileRequest(Request $request, $fileID)
    {
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'requester_position' => 'required|string|max:255',
            'requester_phone' => 'required|string|max:20',
            'requester_email' => 'required|email|max:255',
            'return_date' => 'required|date|after_or_equal:today',
        ], [
            'requester_name.required' => 'Nama pemohon diperlukan',
            'requester_position.required' => 'Jawatan pemohon diperlukan',
            'requester_phone.required' => 'Nombor telefon pemohon diperlukan',
            'requester_email.required' => 'Emel pemohon diperlukan',
            'requester_email.email' => 'Emel tidak sah',
            'return_date.required' => 'Tarikh pulang diperlukan',
            'return_date.after_or_equal' => 'Tarikh pulang mestilah hari ini atau selepasnya',
        ]);

        try {
            DB::beginTransaction();

            // Find or create staff
            $staff = Staff::firstOrCreate(
                ['staffEmail' => $request->requester_email],
                [
                    'staffName' => $request->requester_name,
                    'staffPosition' => $request->requester_position,
                    'staffPhone' => $request->requester_phone,
                ]
            );

            // Get the file first
            $file = File::findOrFail($fileID);

            // Create file request
            $fileRequest = FileRequest::create([
                'fileID' => $fileID,
                'staffID' => $staff->staffID,
                'requestDate' => now(),
            ]);

            // Create file return record with default values
            $fileReturn = FileReturn::create([
                'requestID' => $fileRequest->requestID,
                'userID' => Auth::id(),
                'returnDate' => $request->return_date,
                'returnStatus' => 'Belum Dipulangkan',
                'returnTiming' => 'Tepat', // Default value using actual column name
                'updatedReturnDate' => null // Using actual column name
            ]);

            DB::commit();

            return redirect()->back()
                ->with([
                    'show_success_modal' => true,
                    'request_details' => [
                        'request_id' => $fileRequest->requestID,
                        'requester_name' => $staff->staffName,
                        'file_code' => $file->functionCode . '-' . $file->activityCode . '/' . $file->subActivityCode . '/' . $file->fileCode,
                        'file_name' => $file->fileName,
                        'return_date' => $request->return_date,
                    ],
                    'success' => 'Permohonan fail berjaya dihantar!'
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghantar permohonan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getRequestDetails($requestID)
    {
        $request = FileRequest::with(['staff', 'file', 'fileReturn'])
            ->findOrFail($requestID);

        return response()->json([
            'success' => true,
            'data' => [
                'request_id' => $request->requestID,
                'staff_name' => $request->staff->staffName,
                'staff_position' => $request->staff->staffPosition,
                'staff_phone' => $request->staff->staffPhone,
                'staff_email' => $request->staff->staffEmail,
                'file_code' => $request->file->functionCode . '-' . $request->file->activityCode . '/' . $request->file->subActivityCode . '/' . $request->file->fileCode,
                'file_name' => $request->file->fileName,
                'request_date' => $request->requestDate->format('d/m/Y'),
                'return_date' => $request->fileReturn->returnDate->format('d/m/Y'),
                'status' => $request->status,
            ],
            'message' => 'Maklumat permohonan berjaya diperoleh'
        ]);
    }

    public function reserveFileForm($fileID)
    {
        $file = File::findOrFail($fileID);
        
        // Check if there's already an active reservation for this file
        $hasActiveReservation = FileRequest::where('fileID', $fileID)
            ->where('isReservation', true)
            ->where('reserveStatus', 'Dalam Proses')
            ->exists();

        // Check if there's an ongoing loan from a reservation
        $hasOngoingReservationLoan = FileRequest::where('fileID', $fileID)
            ->where('isReservation', true)
            ->where('reserveStatus', 'Berjaya')
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->exists();

        // Redirect back if there's already an active reservation or ongoing reservation loan
        if ($hasActiveReservation) {
            return redirect()->route('admin.manageRequest')
                ->with('error', 'Fail ini sudah mempunyai tempahan yang aktif.');
        }

        if ($hasOngoingReservationLoan) {
            return redirect()->route('admin.manageRequest')
                ->with('error', 'Fail ini sedang dipinjam dari tempahan yang telah diluluskan.');
        }
        
        // Get the current borrower information
        $currentBorrower = $file->fileRequests()
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->with(['staff', 'fileReturn'])
            ->first();

        return view('Admin.reserveFileForm', compact('file', 'currentBorrower'));
    }

    public function submitFileReservation(Request $request, $fileID)
    {
        // Get the current borrower to determine the minimum reservation date
        $file = File::findOrFail($fileID);
        $currentBorrower = $file->fileRequests()
            ->whereHas('fileReturn', function($query) {
                $query->where('returnStatus', 'Belum Dipulangkan');
            })
            ->with(['staff', 'fileReturn'])
            ->first();

        // Set minimum date validation based on current borrower's return date
        if ($currentBorrower) {
            $returnDate = $currentBorrower->fileReturn->returnDate;
            // Ensure we have a Carbon instance
            if (is_string($returnDate)) {
                $returnDate = Carbon::parse($returnDate);
            } elseif (!$returnDate instanceof Carbon) {
                $returnDate = Carbon::instance($returnDate);
            }
            $minDate = $returnDate->format('Y-m-d'); // Allow same date as return date
            $returnDateFormatted = $returnDate->format('d/m/Y');
        } else {
            $minDate = date('Y-m-d');
            $returnDateFormatted = null;
        }
        
        $request->validate([
            'requester_name' => 'required|string|max:255',
            'requester_position' => 'required|string|max:255',
            'requester_phone' => 'required|string|max:20',
            'requester_email' => 'required|email|max:255',
            'reserve_date' => "required|date|after_or_equal:$minDate",
        ], [
            'requester_name.required' => 'Nama pemohon diperlukan',
            'requester_position.required' => 'Jawatan pemohon diperlukan',
            'requester_phone.required' => 'Nombor telefon pemohon diperlukan',
            'requester_email.required' => 'Emel pemohon diperlukan',
            'requester_email.email' => 'Emel tidak sah',
            'reserve_date.required' => 'Tarikh tempahan diperlukan',
            'reserve_date.after_or_equal' => $currentBorrower 
                ? 'Tarikh tempahan mestilah pada atau selepas tarikh jangka pulang peminjam semasa (' . $returnDateFormatted . ')'
                : 'Tarikh tempahan mestilah hari ini atau selepasnya',
        ]);

        try {
            DB::beginTransaction();

            // Check if there's already an active reservation for this file
            $hasActiveReservation = FileRequest::where('fileID', $fileID)
                ->where('isReservation', true)
                ->where('reserveStatus', 'Dalam Proses')
                ->exists();

            // Check if there's an ongoing loan from a reservation
            $hasOngoingReservationLoan = FileRequest::where('fileID', $fileID)
                ->where('isReservation', true)
                ->where('reserveStatus', 'Berjaya')
                ->whereHas('fileReturn', function($query) {
                    $query->where('returnStatus', 'Belum Dipulangkan');
                })
                ->exists();

            if ($hasActiveReservation) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Fail ini sudah mempunyai tempahan yang aktif.')
                    ->withInput();
            }

            if ($hasOngoingReservationLoan) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Fail ini sedang dipinjam dari tempahan yang telah diluluskan.')
                    ->withInput();
            }

            // Find or create staff
            $staff = Staff::firstOrCreate(
                ['staffEmail' => $request->requester_email],
                [
                    'staffName' => $request->requester_name,
                    'staffPosition' => $request->requester_position,
                    'staffPhone' => $request->requester_phone,
                ]
            );

            // Create file reservation request
            $fileRequest = FileRequest::create([
                'fileID' => $fileID,
                'staffID' => $staff->staffID,
                'requestDate' => now(),
                'reserveStatus' => FileRequest::STATUS_DALAM_PROSES,
                'reserveDate' => $request->reserve_date,
                'isReservation' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.requestStatus')
                ->with([
                    'show_success_modal' => true,
                    'success' => 'Tempahan fail berjaya dihantar!'
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghantar tempahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}