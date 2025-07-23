<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur as User;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:super-admin');
    }

    /**
     * Show the super admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les statistiques
        $stats = [
            'adminCount' => User::role('admin')->count(),
            'userCount' => User::count(),
            'superAdminCount' => User::role('super-admin')->count(),
            'systemActivities' => collect([]) // À remplacer par votre modèle d'activité
        ];

        return view('super-admin.dashboard.index', $stats);
    }
}
