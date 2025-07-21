<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->type_utilisateur == 1) {
            return view('superadmin.dashboard');
        } elseif ($user->type_utilisateur == 2) {
            return view('admin_organisme.dashboard');
        }

        abort(403, 'Accès non autorisé');
    }
}
