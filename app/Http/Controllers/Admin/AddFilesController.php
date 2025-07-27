<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Functions;
use App\Models\Activity;
use App\Models\SubActivity;
use App\Models\File;
use Illuminate\Http\Request;

class AddFilesController extends Controller
{
    /**
     * Show the form for creating a new file.
     */
    public function create()
    {
        try {
            $functions = Functions::all();
            return view('admin.ffaddFile', compact('functions'));
        } catch (\Exception $e) {
            \Log::error('Error in AddFilesController@create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terdapat ralat semasa memuatkan halaman tambah fail.');
        }
    }

    public function getActivities($functionCode)
    {
        try {
            $activities = Activity::where('functionCode', $functionCode)->get();
            return response()->json($activities);
        } catch (\Exception $e) {
            \Log::error('Error fetching activities: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch activities'], 500);
        }
    }

    public function getSubActivities($activityCode)
    {
        try {
            // Get the functionCode from the request or extract from composite key
            $functionCode = request('functionCode');
            
            if ($functionCode) {
                // Query with both activityCode and functionCode
                $subActivities = SubActivity::where('activityCode', $activityCode)
                    ->where('functionCode', $functionCode)
                    ->get();
            } else {
                // Fallback to query with just activityCode if functionCode not provided
                $subActivities = SubActivity::where('activityCode', $activityCode)->get();
            }
            
            return response()->json($subActivities);
        } catch (\Exception $e) {
            \Log::error('Error fetching sub-activities: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch sub-activities'], 500);
        }
    }

    public function store(Request $request)
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

            // Check if file already exists
            $existingFile = File::where('functionCode', $request->functionCode)
                ->where('activityCode', $request->activityCode)
                ->where('subActivityCode', $request->subActivityCode)
                ->where('fileCode', $request->fileCode)
                ->first();

            if ($existingFile) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Fail dengan kod ini sudah wujud.']);
                }
                return redirect()->back()->with('error', 'Fail dengan kod ini sudah wujud.');
            }

            // Handle fileLevel - if "NA" (Tiada Klasifikasi) is selected, store as "Biasa"
            $fileLevel = $request->fileLevel;
            if ($fileLevel === 'NA') {
                $fileLevel = 'Biasa';
            }

            $file = new File();
            $file->functionCode = $request->functionCode;
            $file->activityCode = $request->activityCode;
            $file->subActivityCode = $request->subActivityCode;
            $file->fileCode = $request->fileCode;
            $file->fileName = $request->fileName;
            $file->fileLevel = $fileLevel;
            $file->fileLocation = $request->fileLocation;
            $file->fileDescription = $request->fileDescription;
            $file->fileStatus = 'Aktif'; // Default status is "Aktif"
            $file->save();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Fail baru berjaya ditambah.']);
            }
            
            return redirect()->route('admin.manageFiles')->with('success', 'Fail baru berjaya ditambah.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Sila lengkapkan semua medan yang diperlukan.', 'errors' => $e->errors()]);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error in AddFilesController@store: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terdapat ralat semasa menyimpan fail. Sila cuba lagi.']);
            }
            return redirect()->back()->with('error', 'Terdapat ralat semasa menyimpan fail. Sila cuba lagi.');
        }
    }

    public function addFunction(Request $request)
    {
        $validated = $request->validate([
            'functionCode' => 'required|unique:functions,functionCode',
            'functionName' => 'required',
        ]);

        $function = new Functions();
        $function->functionCode = $request->functionCode;
        $function->functionName = $request->functionName;
        $function->save();

        return response()->json(['success' => true, 'function' => $function]);
    }

    public function addActivity(Request $request)
    {
        $validated = $request->validate([
            'functionCode' => 'required|exists:functions,functionCode',
            'activityCode' => 'required',
            'activityName' => 'required',
        ]);

        // Check if activity already exists
        $existingActivity = Activity::where('functionCode', $request->functionCode)
            ->where('activityCode', $request->activityCode)
            ->first();

        if ($existingActivity) {
            return response()->json(['success' => false, 'message' => 'Aktiviti dengan kod ini sudah wujud untuk fungsi ini.']);
        }

        $activity = new Activity();
        $activity->functionCode = $request->functionCode;
        $activity->activityCode = $request->activityCode;
        $activity->activityName = $request->activityName;
        $activity->save();

        return response()->json(['success' => true, 'activity' => $activity]);
    }

    public function addSubActivity(Request $request)
    {
        $validated = $request->validate([
            'functionCode' => 'required|exists:functions,functionCode',
            'activityCode' => 'required',
            'subActivityCode' => 'required',
            'subActivityName' => 'required',
        ]);

        // Check if sub activity already exists
        $existingSubActivity = SubActivity::where('functionCode', $request->functionCode)
            ->where('activityCode', $request->activityCode)
            ->where('subActivityCode', $request->subActivityCode)
            ->first();

        if ($existingSubActivity) {
            return response()->json(['success' => false, 'message' => 'Sub aktiviti dengan kod ini sudah wujud untuk aktiviti ini.']);
        }

        $subActivity = new SubActivity();
        $subActivity->functionCode = $request->functionCode;
        $subActivity->activityCode = $request->activityCode;
        $subActivity->subActivityCode = $request->subActivityCode;
        $subActivity->subActivityName = $request->subActivityName;
        $subActivity->save();

        return response()->json(['success' => true, 'subActivity' => $subActivity]);
    }
}