<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileRequest;
use App\Models\FileReturn;
use App\Models\Staff;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class ManageReturnController extends Controller
{
    public function index()
    {
        return view('Admin.manageReturn');
    }

    public function getReturnsData()
    {
        $currentUser = auth()->user();
        
        // Only show files that haven't been returned yet
        $query = FileReturn::with(['fileRequest.file', 'fileRequest.staff'])
            ->where('returnStatus', 'Belum Dipulangkan');
            
        // Filter by user role - PKA users can only see files they handle
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        // Admin users can see all pending returns (no additional filtering)
        
        $returns = $query->orderBy('returnDate', 'asc')->get();

        return DataTables::of($returns)
            ->addIndexColumn()
            ->addColumn('file_info', function($return) {
                $file = $return->fileRequest->file;
                return $file->functionCode . '-' . $file->activityCode . '/' . 
                    $file->subActivityCode . '/' . $file->fileCode . ' - ' . 
                    $file->fileName;
            })
            ->addColumn('request_date', function($return) {
                // For reservations, show reserve date; for regular requests, show request date
                if ($return->fileRequest->isReservation && $return->fileRequest->reserveDate) {
                    $date = date('d/m/Y', strtotime($return->fileRequest->reserveDate));
                    return $date . ' <small class="text-info">(Tempahan)</small>';
                } else {
                    $date = $return->fileRequest->requestDate 
                        ? date('d/m/Y', strtotime($return->fileRequest->requestDate))
                        : '';
                    return $date . ' <small class="text-secondary">(Permohonan)</small>';
                }
            })
            ->addColumn('return_status', function($return) {
                $badgeClass = $return->returnStatus === 'Dipulangkan' 
                    ? 'badge-returned' 
                    : 'badge-pending';
                return '<span class="badge '.$badgeClass.'">'.$return->returnStatus.'</span>';
            })
            ->addColumn('return_date_display', function($return) {
                $date = date('d/m/Y', strtotime($return->returnDate));
                $editIcon = '';
                $timingBadge = '';
                
                if ($return->returnStatus !== 'Dipulangkan') {
                    $editIcon = '<i class="fas fa-pencil-alt return-date-edit ml-2" 
                                data-id="'.$return->returnID.'" 
                                data-date="'.date('Y-m-d', strtotime($return->returnDate)).'"></i>';
                }
                
                if ($return->returnStatus === 'Dipulangkan') {
                    $badgeClass = 'badge-info';
                    if ($return->returnTiming === 'Lewat') {
                        $badgeClass = 'badge-danger';
                    } elseif ($return->returnTiming === 'Awal') {
                        $badgeClass = 'badge-success';
                    }
                    $timingBadge = '<div class="mt-1"><span class="badge '.$badgeClass.'">'.$return->returnTiming.'</span></div>';
                }
                
                return '<div>'.
                    '<span class="return-date">'.$date.'</span>'.$editIcon.
                    $timingBadge.
                    '</div>';
            })
            ->addColumn('updated_return_date', function($return) {
                if ($return->returnStatus === 'Dipulangkan') {
                    if ($return->returnTiming === 'Tepat') {
                        return '<span class="text-muted">- Tepat Pada Tarikh -</span>';
                    }
                    return $return->updatedReturnDate 
                        ? date('d/m/Y', strtotime($return->updatedReturnDate))
                        : '<span class="text-danger">Tiada rekod</span>';
                }
                return '<span class="text-muted">-</span>';
            })
            ->addColumn('staff_details', function($return) {
                return '<button class="btn btn-sm btn-info view-staff-btn" data-id="'.$return->returnID.'">
                        <i class="fas fa-eye"></i> Lihat</button>';
            })
            ->addColumn('action', function($return) {
                if ($return->returnStatus !== 'Dipulangkan') {
                    return '<button class="btn btn-sidebar btn-sm return-btn" data-id="'.$return->returnID.'">
                            <i class="fas fa-check"></i> Terima</button>';
                }
                return '<button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-check"></i> Selesai</button>';
            })
            ->rawColumns(['request_date', 'return_status', 'return_date_display', 'updated_return_date', 'staff_details', 'action'])
            ->make(true);
    }

    public function updateReturnDate(Request $request, $id)
    {
        $request->validate([
            'returnDate' => 'required|date'
        ]);

        $currentUser = auth()->user();
        $query = FileReturn::where('returnID', $id);
        
        // Apply role-based filtering
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        
        $return = $query->first();
        
        if (!$return) {
            return response()->json(['error' => 'Tidak dibenarkan atau rekod tidak dijumpai'], 403);
        }
        
        // Only allow update if not yet returned
        if ($return->returnStatus !== 'Returned') {
            $return->update([
                'returnDate' => $request->returnDate
            ]);

            return response()->json(['success' => 'Tarikh pulang berjaya dikemaskini']);
        }

        return response()->json(['error' => 'Fail sudah dipulangkan'], 400);
    }

    public function getReturnDetails($id)
    {
        try {
            $currentUser = auth()->user();
            $query = FileReturn::with(['fileRequest.file']);
            
            // Apply role-based filtering
            if ($currentUser && $currentUser->role === 'Pka') {
                $query->where('userID', $currentUser->userID);
            }
            
            $return = $query->find($id);
            
            if (!$return) {
                return response()->json(['error' => 'Tidak dibenarkan atau rekod tidak dijumpai'], 403);
            }

            return response()->json([
                'requestID' => $return->fileRequest->requestID,
                'file_info' => $return->fileRequest->file->functionCode . '-' . 
                              $return->fileRequest->file->activityCode . '/' . 
                              $return->fileRequest->file->subActivityCode . '/' . 
                              $return->fileRequest->file->fileCode . ' - ' . 
                              $return->fileRequest->file->fileName
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting return details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get return details'], 500);
        }
    }

    public function processReturn(Request $request, $id)
    {
        try {
            $currentUser = auth()->user();
            $query = FileReturn::with('fileRequest.file');
            
            // Apply role-based filtering
            if ($currentUser && $currentUser->role === 'Pka') {
                $query->where('userID', $currentUser->userID);
            }
            
            $return = $query->find($id);
            
            if (!$return) {
                return response()->json(['error' => 'Tidak dibenarkan atau rekod tidak dijumpai'], 403);
            }
            
            if ($return->returnStatus === 'Dipulangkan') {
                return response()->json(['error' => 'Fail sudah dipulangkan'], 400);
            }

            // Get dates without time components
            $returnDate = new \DateTime($return->returnDate);
            $returnDate->setTime(0, 0, 0); // Normalize to midnight
            
            $currentDate = new \DateTime();
            $currentDate->setTime(0, 0, 0); // Normalize to midnight
            
            $returnTiming = 'Tepat';
            $updatedReturnDate = null;
            
            if ($currentDate < $returnDate) {
                $returnTiming = 'Awal';
                $updatedReturnDate = (new \DateTime())->format('Y-m-d');
            } elseif ($currentDate > $returnDate) {
                $returnTiming = 'Lewat';
                $updatedReturnDate = (new \DateTime())->format('Y-m-d');
            } else {
                // Dates are exactly equal (same day)
                $returnTiming = 'Tepat';
                $updatedReturnDate = $return->returnDate; // Keep original date
            }
            
            \DB::transaction(function () use ($return, $returnTiming, $updatedReturnDate) {
                $file = $return->fileRequest->file;
                $file->fileStatus = 'Tersedia';
                $file->save();
                
                $return->returnStatus = 'Dipulangkan';
                $return->returnTiming = $returnTiming; // Use the actual column name
                $return->updatedReturnDate = $updatedReturnDate; // Use the actual column name
                $return->save();
            });

            return response()->json(['success' => 'Pemulangan fail berjaya direkodkan']);
            
        } catch (\Exception $e) {
            Log::error('Error processing return: ' . $e->getMessage());
            return response()->json(['error' => 'Ralat sistem semasa memproses pemulangan'], 500);
        }
    }

    public function getStaffDetails($id)
    {
        $currentUser = auth()->user();
        $query = FileReturn::with('fileRequest.staff');
        
        // Apply role-based filtering
        if ($currentUser && $currentUser->role === 'Pka') {
            $query->where('userID', $currentUser->userID);
        }
        
        $return = $query->find($id);
        
        if (!$return) {
            return response()->json(['error' => 'Tidak dibenarkan atau rekod tidak dijumpai'], 403);
        }
        
        $staff = $return->fileRequest->staff;
        
        return response()->json([
            'name' => $staff->staffName,
            'phone' => $staff->staffPhone,
            'email' => $staff->staffEmail,
            'position' => $staff->staffPosition
        ]);
    }
}