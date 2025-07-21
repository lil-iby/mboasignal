<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('role:admin|super-admin');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Aucune dépendance à Auth, retourne toujours des données factices
        return $this->fallbackDashboard();
    }
    
    /**
     * Retourne un tableau de bord avec des données factices en cas d'erreur
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function fallbackDashboard()
    {
        return view('admin.dashboard', [
            'userCount' => 0,
            'contentCount' => 0,
            'activityCount' => 0,
            'recentActivities' => [],
            'usageStats' => [
                'labels' => [],
                'datasets' => []
            ]
        ]);
    }
}
