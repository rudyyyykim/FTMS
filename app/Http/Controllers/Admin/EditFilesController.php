<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Functions;
use App\Models\Activity;
use App\Models\SubActivity;
use Illuminate\Http\Request;

class EditFilesController extends Controller
{
    public function edit($id)
    {
        $file = File::with(['functions', 'activity', 'subActivity'])->findOrFail($id);
        $functions = Functions::all();
        $activities = Activity::where('functionCode', $file->functionCode)->get();
        $subActivities = SubActivity::where('activityCode', $file->activityCode)
            ->where('functionCode', $file->functionCode)
            ->get();

        return view('Admin.ffeditFile', compact('file', 'functions', 'activities', 'subActivities'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'functionCode' => 'required',
                'activityCode' => 'required',
                'subActivityCode' => 'required',
                'fileCode' => 'required',
                'fileName' => 'required',
                'fileLevel' => 'required',
                'fileLocation' => 'required',
                'fileDescription' => 'nullable',
            ]);

            $file = File::findOrFail($id);

            // Check if file code is changed and already exists
            if ($file->fileCode != $request->fileCode || 
                $file->functionCode != $request->functionCode || 
                $file->activityCode != $request->activityCode || 
                $file->subActivityCode != $request->subActivityCode) {
                
                $existingFile = File::where('functionCode', $request->functionCode)
                    ->where('activityCode', $request->activityCode)
                    ->where('subActivityCode', $request->subActivityCode)
                    ->where('fileCode', $request->fileCode)
                    ->where('fileID', '!=', $id)
                    ->first();

                if ($existingFile) {
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Fail dengan kod ini sudah wujud.']);
                    }
                    return redirect()->back()->with('error', 'Fail dengan kod ini sudah wujud.');
                }
            }

            // Handle fileLevel - if "NA" (Tiada Klasifikasi) is selected, store as "Biasa"
            $fileLevel = $request->fileLevel;
            if ($fileLevel === 'NA') {
                $fileLevel = 'Biasa';
            }

            $file->functionCode = $request->functionCode;
            $file->activityCode = $request->activityCode;
            $file->subActivityCode = $request->subActivityCode;
            $file->fileCode = $request->fileCode;
            $file->fileName = $request->fileName;
            $file->fileLevel = $fileLevel;
            $file->fileLocation = $request->fileLocation;
            $file->fileDescription = $request->fileDescription;
            $file->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Fail berjaya dikemaskini.']);
            }

            return redirect()->route('admin.manageFiles')->with('success', 'Fail berjaya dikemaskini.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sila lengkapkan semua medan yang diperlukan.', 'errors' => $e->errors()]);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error in EditFilesController@update: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terdapat ralat semasa mengemaskini fail. Sila cuba lagi.']);
            }
            return redirect()->back()->with('error', 'Terdapat ralat semasa mengemaskini fail. Sila cuba lagi.');
        }
    }
}