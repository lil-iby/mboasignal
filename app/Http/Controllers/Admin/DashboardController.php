<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin_organisme.dashboard'); // Correspond au fichier resources/views/admin_organisme/dashboard.blade.php
    }
}
