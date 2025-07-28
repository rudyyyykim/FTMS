<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FileRequest;
use App\Models\FileReturn;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RequestStatusController extends Controller
{
    public function index()
    {
        return view('Admin.requestStatus');
    }

    public function data(Request $request)
    {
        try {
            // Get reservations that are in "Dalam Proses" status
            $reservations = FileRequest::with(['file', 'staff'])
                ->where('isReservation', true)
                ->where('reserveStatus', FileRequest::STATUS_DALAM_PROSES)
                ->get();

            if ($reservations->isEmpty()) {
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => []
                ]);
            }

            // Manual processing without DataTables package issues
            $data = [];
            $i = 1;
            foreach ($reservations as $reservation) {
                $fileInfo = 'File not found';
                if ($reservation->file) {
                    $fileInfo = $reservation->file->functionCode . '-' . $reservation->file->activityCode . '/' . 
                               $reservation->file->subActivityCode . '/' . $reservation->file->fileCode . ' - ' . 
                               $reservation->file->fileName;
                }

                $cancelBtn = '<button class="btn btn-danger btn-sm me-1" onclick="cancelReservation(' . $reservation->requestID . ')">Batal</button>';
                $proceedBtn = '<button class="btn btn-success btn-sm" onclick="proceedReservation(' . $reservation->requestID . ')">Teruskan</button>';

                $data[] = [
                    'DT_RowIndex' => $i++,
                    'request_id' => $reservation->requestID,
                    'borrower_name' => $reservation->staff ? $reservation->staff->staffName : 'N/A',
                    'file_code' => $fileInfo,
                    'request_date' => $reservation->requestDate ? $reservation->requestDate->format('d/m/Y') : '-',
                    'reserve_date' => $reservation->reserveDate ? $reservation->reserveDate->format('d/m/Y') : '-',
                    'action' => $cancelBtn . $proceedBtn
                ];
            }

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('RequestStatus data error: ' . $e->getMessage() . ' at line ' . $e->getLine() . ' in file ' . $e->getFile());
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error loading data: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelReservation(Request $request, $requestID)
    {
        try {
            $fileRequest = FileRequest::findOrFail($requestID);
            $fileRequest->setDibatalkan();

            return response()->json([
                'success' => true,
                'message' => 'Tempahan berjaya dibatalkan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan tempahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRequestInfo(Request $request, $requestID)
    {
        try {
            $fileRequest = FileRequest::findOrFail($requestID);
            
            return response()->json([
                'success' => true,
                'reserve_date' => $fileRequest->reserveDate->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan maklumat tempahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function proceedReservation(Request $request, $requestID)
    {
        try {
            DB::beginTransaction();

            $fileRequest = FileRequest::with('file')->findOrFail($requestID);
            
            // Debug logging
            \Log::info('Processing reservation', [
                'requestID' => $requestID,
                'reserveDate' => $fileRequest->reserveDate,
                'reserveDate_formatted' => $fileRequest->reserveDate->format('Y-m-d'),
                'returnDate' => $request->return_date,
                'validation_rule' => 'after:' . $fileRequest->reserveDate->format('Y-m-d')
            ]);
            
            // Simple validation first
            if (!$request->return_date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarikh jangka pulang diperlukan'
                ], 422);
            }
            
            // Check if return date is after reserve date
            $reserveDate = $fileRequest->reserveDate->format('Y-m-d');
            $returnDate = $request->return_date;
            
            if ($returnDate <= $reserveDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarikh jangka pulang mestilah selepas tarikh tempahan (' . $fileRequest->reserveDate->format('d/m/Y') . ')'
                ], 422);
            }
            
            // Check if the file is still borrowed
            $currentBorrower = $fileRequest->file->fileRequests()
                ->whereHas('fileReturn', function($query) {
                    $query->where('returnStatus', 'Belum Dipulangkan');
                })
                ->first();

            if ($currentBorrower) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fail masih belum dipulangkan oleh peminjam semasa. Sila pastikan fail telah dipulangkan sebelum meneruskan tempahan.'
                ], 400);
            }

            // Create file return record for the reservation with provided return date
            $fileReturn = FileReturn::create([
                'requestID' => $fileRequest->requestID,
                'userID' => auth()->id(),
                'returnDate' => Carbon::parse($request->return_date),
                'returnStatus' => 'Belum Dipulangkan',
                'returnTiming' => 'Tepat',
                'updatedReturnDate' => null
            ]);

            // Update reservation status to success
            $fileRequest->setBerjaya();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tempahan berjaya diproses. Sila hubungi staff ' . $fileRequest->staff->staffName . ' untuk mengambil fail.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in proceedReservation', [
                'requestID' => $requestID,
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->errors()['return_date'][0] ?? 'Tarikh jangka pulang tidak sah.'
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in proceedReservation', [
                'requestID' => $requestID,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses tempahan: ' . $e->getMessage()
            ], 500);
        }
    }
}