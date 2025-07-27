<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Functions;

class FunctionController extends Controller
{
    public function showFunctions()
    {
        // Fetch all functions from the database
        $functions = Functions::all()->map(function($function) {
            return [
                'name' => $function->functionName,
                'description' => $function->functionDescription,
                'slug' => $function->functionCode
            ];
        });
        return view('admin.selectFunction', compact('functions'));
    }
}
