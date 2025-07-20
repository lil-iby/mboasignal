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
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|super-admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Récupérer le token d'authentification de l'utilisateur connecté
        $token = Auth::user()->createToken('dashboard-token')->plainTextToken;
        
        // Configuration pour les appels API
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
        
        try {
            // Récupérer les statistiques depuis l'API
            $statsResponse = Http::withHeaders($headers)
                ->get(config('app.url') . '/api/v1/dashboard/stats');
                
            $recentActivitiesResponse = Http::withHeaders($headers)
                ->get(config('app.url') . '/api/v1/dashboard/recent-activities');
                
            $usageStatsResponse = Http::withHeaders($headers)
                ->get(config('app.url') . '/api/v1/dashboard/usage-stats');
            
            // Vérifier si les réponses sont valides
            if ($statsResponse->successful() && $recentActivitiesResponse->successful() && $usageStatsResponse->successful()) {
                $stats = $statsResponse->json();
                $recentActivities = $recentActivitiesResponse->json();
                $usageStats = $usageStatsResponse->json();
                
                return view('admin.dashboard.index', array_merge($stats, [
                    'recentActivities' => $recentActivities,
                    'usageStats' => $usageStats,
                ]));
            }
            
            // En cas d'erreur, utiliser des données factices
            return $this->fallbackDashboard();
            
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser des données factices
            return $this->fallbackDashboard();
        }
    }
    
    /**
     * Retourne un tableau de bord avec des données factices en cas d'erreur
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function fallbackDashboard()
    {
        return view('admin.dashboard.index', [
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
