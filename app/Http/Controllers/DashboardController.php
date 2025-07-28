<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function adminIndex()
    {
        return view('Admin.dashboard'); // Create this view
    }

    public function supervisorIndex()
    {
        return view('supervisor.dashboard'); // Existing view
    }
}
