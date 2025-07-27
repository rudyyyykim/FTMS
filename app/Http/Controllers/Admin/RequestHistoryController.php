<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FileReturn;
use App\Models\FileRequest;
use App\Models\File;
use App\Models\Staff;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Exception;

class RequestHistoryController extends Controller
{
    public function index()
    {
        return view('Admin.requestHistory');
    }

    public function getHistoryData(Request $request)
    {
        // Get only returned files (status = 'Dipulangkan')
        $query = FileReturn::with([
            'fileRequest.file.functions',
            'fileRequest.file.activity', 
            'fileRequest.file.subActivity',
            'fileRequest.staff',
            'user'
        ])
        ->where('returnStatus', 'Dipulangkan')
        ->orderBy('updated_at', 'desc');

        // Filter by user role - Pka users can only see their own handled requests
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        // Admin users can see all records (no additional filtering)

        // Apply filters if provided
        if ($request->has('status') && $request->status != '') {
            // For history, we mainly show 'Dipulangkan' status
            $query->where('returnStatus', $request->status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereHas('fileRequest', function($q) use ($request) {
                $q->whereDate('requestDate', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereHas('fileRequest', function($q) use ($request) {
                $q->whereDate('requestDate', '<=', $request->date_to);
            });
        }

        if ($request->has('file_code') && $request->file_code != '') {
            $query->whereHas('fileRequest.file', function($q) use ($request) {
                $fileCodeParts = explode('-', $request->file_code);
                if (count($fileCodeParts) >= 2) {
                    $functionCode = $fileCodeParts[0];
                    $restParts = explode('/', $fileCodeParts[1]);
                    if (count($restParts) >= 3) {
                        $activityCode = $restParts[0];
                        $subActivityCode = $restParts[1];
                        $fileCode = $restParts[2];
                        
                        $q->where('functionCode', $functionCode)
                          ->where('activityCode', $activityCode)
                          ->where('subActivityCode', $subActivityCode)
                          ->where('fileCode', $fileCode);
                    }
                } else {
                    // If format doesn't match, search in all code fields
                    $q->where(function($subQ) use ($request) {
                        $subQ->where('functionCode', 'like', '%' . $request->file_code . '%')
                             ->orWhere('activityCode', 'like', '%' . $request->file_code . '%')
                             ->orWhere('subActivityCode', 'like', '%' . $request->file_code . '%')
                             ->orWhere('fileCode', 'like', '%' . $request->file_code . '%');
                    });
                }
            });
        }

        if ($request->has('staff_name') && $request->staff_name != '') {
            $query->whereHas('fileRequest.staff', function($q) use ($request) {
                $q->where('staffName', 'like', '%' . $request->staff_name . '%');
            });
        }

        if ($request->has('timing') && $request->timing != '') {
            $query->where('returnTiming', $request->timing);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('request_id', function($return) {
                return $return->fileRequest->requestID ?? '-';
            })
            ->addColumn('request_date', function($return) {
                return $return->fileRequest->requestDate ? 
                    $return->fileRequest->requestDate->format('d/m/Y') : '-';
            })
            ->addColumn('return_id', function($return) {
                return $return->returnID ?? '-';
            })
            ->addColumn('actual_return_date', function($return) {
                return $return->updatedReturnDate ? 
                    Carbon::parse($return->updatedReturnDate)->format('d/m/Y') : 
                    ($return->returnDate ? $return->returnDate->format('d/m/Y') : '-');
            })
            ->addColumn('file_info', function($return) {
                $file = $return->fileRequest->file;
                if (!$file) return '-';
                
                $fileCode = $file->functionCode . '-' . $file->activityCode . '/' . 
                           $file->subActivityCode . '/' . $file->fileCode;
                $fileName = $file->fileName;
                $location = $file->fileLocation ?? 'Tidak dinyatakan';
                
                return [
                    'file_code' => $fileCode,
                    'file_name' => $fileName,
                    'file_location' => $location,
                    'file_description' => $file->fileDescription ?? '-',
                    'file_level' => $file->fileLevel ?? '-',
                    'created_by' => 'Admin Sistem' // You might want to track this in the database
                ];
            })
            ->addColumn('staff_info', function($return) {
                $staff = $return->fileRequest->staff;
                if (!$staff) return '-';
                
                return [
                    'staff_name' => $staff->staffName,
                    'staff_phone' => $staff->staffPhone ?? 'Tidak dinyatakan',
                    'staff_email' => $staff->staffEmail ?? 'Tidak dinyatakan',
                    'staff_position' => $staff->staffPosition ?? 'Tidak dinyatakan'
                ];
            })
            ->addColumn('return_timing', function($return) {
                // Use the returnTiming column value directly from database
                $timing = $return->returnTiming ?? 'Tepat';
                
                if ($timing === 'Awal') {
                    $badgeClass = 'badge-success';
                } elseif ($timing === 'Lewat') {
                    $badgeClass = 'badge-danger';
                } else {
                    $badgeClass = 'badge-info';
                }
                
                return '<span class="badge ' . $badgeClass . '">' . $timing . '</span>';
            })
            ->addColumn('handled_by', function($return) {
                try {
                    $user = $return->user;
                    return $user ? $user->username : 'Sistem';
                } catch (Exception $e) {
                    return 'Sistem';
                }
            })
            ->filterColumn('file_code', function($query, $keyword) {
                $query->whereHas('fileRequest.file', function($q) use ($keyword) {
                    $q->where(function($subQ) use ($keyword) {
                        $subQ->where('functionCode', 'like', '%' . $keyword . '%')
                             ->orWhere('activityCode', 'like', '%' . $keyword . '%')
                             ->orWhere('subActivityCode', 'like', '%' . $keyword . '%')
                             ->orWhere('fileCode', 'like', '%' . $keyword . '%')
                             ->orWhere('fileName', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->filterColumn('staff_name', function($query, $keyword) {
                $query->whereHas('fileRequest.staff', function($q) use ($keyword) {
                    $q->where('staffName', 'like', '%' . $keyword . '%');
                });
            })
            ->filterColumn('handled_by', function($query, $keyword) {
                $query->whereHas('user', function($q) use ($keyword) {
                    $q->where('username', 'like', '%' . $keyword . '%');
                });
            })
            ->rawColumns(['return_timing'])
            ->make(true);
    }

    public function exportData(Request $request)
    {
        $format = $request->input('format', 'excel');
        
        // Get filtered data based on the same criteria as getHistoryData
        $query = FileReturn::with([
            'fileRequest.file.functions',
            'fileRequest.file.activity', 
            'fileRequest.file.subActivity',
            'fileRequest.staff',
            'user'
        ])
        ->where('returnStatus', 'Dipulangkan')
        ->orderBy('updated_at', 'desc');

        // Filter by user role - Pka users can only see their own handled requests
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        // Admin users can see all records (no additional filtering)

        // Apply the same filters
        if ($request->has('status') && $request->status != '') {
            $query->where('returnStatus', $request->status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereHas('fileRequest', function($q) use ($request) {
                $q->whereDate('requestDate', '>=', $request->date_from);
            });
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereHas('fileRequest', function($q) use ($request) {
                $q->whereDate('requestDate', '<=', $request->date_to);
            });
        }

        if ($request->has('file_code') && $request->file_code != '') {
            $query->whereHas('fileRequest.file', function($q) use ($request) {
                $fileCodeParts = explode('-', $request->file_code);
                if (count($fileCodeParts) >= 2) {
                    $functionCode = $fileCodeParts[0];
                    $restParts = explode('/', $fileCodeParts[1]);
                    if (count($restParts) >= 3) {
                        $activityCode = $restParts[0];
                        $subActivityCode = $restParts[1];
                        $fileCode = $restParts[2];
                        
                        $q->where('functionCode', $functionCode)
                          ->where('activityCode', $activityCode)
                          ->where('subActivityCode', $subActivityCode)
                          ->where('fileCode', $fileCode);
                    }
                }
            });
        }

        if ($request->has('staff_name') && $request->staff_name != '') {
            $query->whereHas('fileRequest.staff', function($q) use ($request) {
                $q->where('staffName', 'like', '%' . $request->staff_name . '%');
            });
        }

        if ($request->has('timing') && $request->timing != '') {
            $query->where('returnTiming', $request->timing);
        }

        $data = $query->get();
        
        // Process data for export
        $exportData = $data->map(function($return) {
            $file = $return->fileRequest->file;
            $staff = $return->fileRequest->staff;
            
            $expectedDate = Carbon::parse($return->returnDate);
            $actualDate = $return->updatedReturnDate ? 
                Carbon::parse($return->updatedReturnDate) : 
                Carbon::parse($return->returnDate);
            
            $timing = $return->returnTiming ?? 'Tepat';
            
            // Prepare file details
            $fileCode = $file->functionCode . '-' . $file->activityCode . '/' . 
                       $file->subActivityCode . '/' . $file->fileCode;
            
            return [
                'ID Permohonan' => $return->fileRequest->requestID,
                'Tarikh Mohon' => $return->fileRequest->requestDate->format('d/m/Y'),
                'ID Pemulangan' => $return->returnID,
                'Tarikh Dijangka Pulang' => $return->returnDate->format('d/m/Y'),
                'Tarikh Sebenar Pulang' => $actualDate->format('d/m/Y'),
                
                // File Details (from popup modal)
                'Kod Fail' => $fileCode,
                'Nama Fail' => $file->fileName,
                'Lokasi Fail' => $file->fileLocation ?? 'Tidak dinyatakan',
                'Penerangan Fail' => $file->fileDescription ?? 'Tidak dinyatakan',
                'Tahap Fail' => $file->fileLevel ?? 'Tidak dinyatakan',
                
                // Staff Details (from popup modal)
                'Nama Staff' => $staff->staffName,
                'Jawatan Staff' => $staff->staffPosition ?? 'Tidak dinyatakan',
                'No. Telefon Staff' => $staff->staffPhone ?? 'Tidak dinyatakan',
                'Email Staff' => $staff->staffEmail ?? 'Tidak dinyatakan',
                
                'Masa Pemulangan' => $timing,
                'Dikendalikan Oleh' => $return->user ? $return->user->username : 'Sistem'
            ];
        });

        // For now, return as JSON. You can implement actual export functionality here
        return response()->json([
            'success' => true,
            'data' => $exportData,
            'format' => $format,
            'message' => 'Data berjaya diekport dalam format ' . $format
        ]);
    }
}