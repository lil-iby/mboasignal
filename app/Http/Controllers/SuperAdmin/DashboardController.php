<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('superadmin.dashboard'); // Correspond au fichier resources/views/superadmin/dashboard.blade.php
    }
}
