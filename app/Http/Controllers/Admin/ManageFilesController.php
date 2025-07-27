<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Functions;
use App\Models\Activity;
use App\Models\SubActivity;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ManageFilesController extends Controller
{
    public function index()
    {
        $functions = Functions::all(); // Get all functions for filter dropdown
        return view('admin.manageFiles', compact('functions'));
    }

    public function getFilesData(Request $request)
    {
        $query = File::with(['functions', 'activity', 'subActivity'])
            ->select('files.*');

        // Apply filters if they exist
        if ($request->has('function_filter') && $request->function_filter) {
            $query->where('files.functionCode', $request->function_filter);
        }

        if ($request->has('security_filter') && $request->security_filter) {
            $query->where('files.fileLevel', $request->security_filter);
        }

        if ($request->has('status_filter') && $request->status_filter) {
            $query->where('files.fileStatus', $request->status_filter);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('function', function ($file) {
                // Safe check for function relationship
                return $file->functions 
                    ? $file->functionCode . ' - ' . $file->functions->functionName 
                    : $file->functionCode . ' - (Fungsi tidak dijumpai)';
            })
            ->addColumn('file_code', function ($file) {
                return $file->functionCode . '-' . $file->activityCode . '/' . $file->subActivityCode . '/' . $file->fileCode;
            })
            ->addColumn('file_name', function ($file) {
                return $file->fileName ?? '-';
            })
            ->addColumn('file_description', function ($file) {
                return $file->fileDescription ?? '-';
            })
            ->addColumn('file_location', function ($file) {
                return $file->fileLocation ?? '-';
            })
            ->addColumn('file_level', function ($file) {
                return $file->fileLevel ?? 'NA';
            })
            ->addColumn('file_status', function ($file) {
                return $file->fileStatus ?? 'Tersedia';
            })
            ->addColumn('description_button', function ($file) {
                $functionName = $file->functions ? $file->functions->functionName : 'Fungsi tidak dijumpai';
                
                $details = '<strong>Fungsi:</strong> ' . $file->functionCode . ' - ' . $functionName . '<br>';
                $details .= '<strong>Kod Fail:</strong> ' . $file->functionCode . '-' . $file->activityCode . '/' . $file->subActivityCode . '/' . $file->fileCode . '<br>';
                $details .= '<strong>Fail:</strong> ' . ($file->fileName ?? '-') . '<br>';
                $details .= '<strong>Keterangan:</strong> ' . ($file->fileDescription ?? '-') . '<br>';
                $details .= '<strong>Lokasi:</strong> ' . ($file->fileLocation ?? '-') . '<br>';
                
                // Add security level with proper text
                $securityLevel = '';
                switch($file->fileLevel) {
                    case 'T': $securityLevel = 'Terhad (T)'; break;
                    case 'S': $securityLevel = 'Sulit (S)'; break;
                    case 'R': $securityLevel = 'Rahsia (R)'; break;
                    case 'RB': $securityLevel = 'Rahsia Besar (RB)'; break;
                    default: $securityLevel = 'Tiada Klasifikasi';
                }
                $details .= '<strong>Peringkat:</strong> ' . $securityLevel . '<br>';
                
                $details .= '<strong>Status:</strong> ' . ($file->fileStatus ?? 'Tersedia') . '<br>';
                
                return '<button class="btn btn-info btn-sm" data-toggle="modal" data-target="#descriptionModal" data-description="'.e($details).'"><i class="fas fa-info-circle"></i> Butiran</button>';
            })
            ->addColumn('edit_button', function ($file) {
                return '<a href="'.route('admin.editFile', $file->fileID).'" class="btn btn-edit btn-sm"><i class="fas fa-edit"></i> Sunting</a>';
            })
            ->addColumn('delete_button', function ($file) {
                return '<button class="btn btn-delete btn-sm delete-file-btn" data-id="'.$file->fileID.'" data-name="'.($file->fileName ?? 'Fail ini').'"><i class="fas fa-trash"></i> Padam</button>';
            })
            // Enable search on specific columns with proper table prefixes
            ->filterColumn('function', function($query, $keyword) {
                $query->whereHas('functions', function($q) use ($keyword) {
                    $q->where('functions.functionName', 'like', "%{$keyword}%")
                      ->orWhere('functions.functionCode', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('file_code', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('files.functionCode', 'like', "%{$keyword}%")
                      ->orWhere('files.activityCode', 'like', "%{$keyword}%")
                      ->orWhere('files.subActivityCode', 'like', "%{$keyword}%")
                      ->orWhere('files.fileCode', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('file_name', function($query, $keyword) {
                $query->where('files.fileName', 'like', "%{$keyword}%");
            })
            ->filterColumn('file_description', function($query, $keyword) {
                $query->where('files.fileDescription', 'like', "%{$keyword}%");
            })
            ->filterColumn('file_location', function($query, $keyword) {
                $query->where('files.fileLocation', 'like', "%{$keyword}%");
            })
            // Enable ordering on specific columns with proper joins and table prefixes
            ->orderColumn('function', function ($query, $order) {
                $query->join('functions', 'files.functionCode', '=', 'functions.functionCode')
                      ->orderBy('functions.functionName', $order);
            })
            ->orderColumn('file_code', function ($query, $order) {
                $query->orderBy('files.functionCode', $order)
                      ->orderBy('files.activityCode', $order)
                      ->orderBy('files.subActivityCode', $order)
                      ->orderBy('files.fileCode', $order);
            })
            ->orderColumn('file_name', function ($query, $order) {
                $query->orderBy('files.fileName', $order);
            })
            ->orderColumn('file_description', function ($query, $order) {
                $query->orderBy('files.fileDescription', $order);
            })
            ->orderColumn('file_location', function ($query, $order) {
                $query->orderBy('files.fileLocation', $order);
            })
            ->rawColumns(['description_button', 'edit_button', 'delete_button'])
            ->make(true);
    }

    public function getFileDetails($id)
    {
        $file = File::with(['functions', 'activity', 'subActivity'])->find($id);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Fail tidak dijumpai'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'file' => $file
        ]);
    }

    public function deleteFile(Request $request, $id)
    {
        $file = File::find($id);
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Fail tidak dijumpai'
            ], 404);
        }

        try {
            $file->delete();
            return response()->json([
                'success' => true,
                'message' => 'Fail berjaya dipadam.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ralat semasa memadam fail: ' . $e->getMessage()
            ], 500);
        }
    }
}