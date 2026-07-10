<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Show Admin Dashboard
     */
    public function index()
    {
        return view('admin.dashboard');
    }
}