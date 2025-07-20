<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Content;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Récupère les statistiques du tableau de bord
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        $userCount = User::count();
        $contentCount = Content::count();
        $todayActivities = ActivityLog::whereDate('created_at', today())->count();
        
        return response()->json([
            'userCount' => $userCount,
            'contentCount' => $contentCount,
            'activityCount' => $todayActivities,
        ]);
    }

    /**
     * Récupère les activités récentes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentActivities()
    {
        $activities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'title' => $this->getActivityTitle($activity->action, $activity->model_type, $activity->model_id),
                    'description' => $activity->description,
                    'icon' => $this->getActivityIcon($activity->action),
                    'created_at' => $activity->created_at->diffForHumans(),
                    'user' => $activity->user ? [
                        'name' => $activity->user->name,
                        'avatar' => $this->getUserAvatar($activity->user)
                    ] : null
                ];
            });

        return response()->json($activities);
    }

    /**
     * Récupère les statistiques d'utilisation
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function usageStats()
    {
        // Statistiques des 7 derniers jours
        $endDate = now();
        $startDate = now()->subDays(6);
        
        $dates = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dates[$currentDate->toDateString()] = 0;
            $currentDate->addDay();
        }
        
        // Utilisateurs par jour
        $usersByDay = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        // Activités par jour
        $activitiesByDay = ActivityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        // Contenu par jour
        $contentByDay = Content::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        // Formater les données pour le graphique
        $labels = array_keys($dates);
        $userData = array_values(array_merge($dates, $usersByDay));
        $activityData = array_values(array_merge($dates, $activitiesByDay));
        $contentData = array_values(array_merge($dates, $contentByDay));
        
        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Utilisateurs',
                    'data' => $userData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
                [
                    'label' => 'Activités',
                    'data' => $activityData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'Contenus',
                    'data' => $contentData,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ]
            ]
        ]);
    }
    
    /**
     * Génère un titre d'activité lisible
     *
     * @param string $action
     * @param string|null $modelType
     * @param int|null $modelId
     * @return string
     */
    protected function getActivityTitle($action, $modelType = null, $modelId = null)
    {
        $titles = [
            'created' => 'Nouvel élément créé',
            'updated' => 'Élément mis à jour',
            'deleted' => 'Élément supprimé',
            'login' => 'Connexion utilisateur',
            'logout' => 'Déconnexion utilisateur',
            'registered' => 'Nouvel utilisateur inscrit',
        ];
        
        if (isset($titles[$action])) {
            return $titles[$action];
        }
        
        return ucfirst($action);
    }
    
    /**
     * Retourne l'icône appropriée pour une action
     *
     * @param string $action
     * @return string
     */
    protected function getActivityIcon($action)
    {
        $icons = [
            'created' => 'plus-circle',
            'updated' => 'edit',
            'deleted' => 'trash',
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'registered' => 'user-plus',
            'default' => 'circle',
        ];
        
        return $icons[$action] ?? $icons['default'];
    }
    
    /**
     * Génère une URL d'avatar pour un utilisateur
     *
     * @param User $user
     * @return string
     */
    protected function getUserAvatar($user)
    {
        if ($user->avatar) {
            return asset('storage/' . $user->avatar);
        }
        
        // Générer une image d'avatar par défaut avec les initiales
        $name = urlencode(ucfirst(substr($user->name, 0, 1)));
        $bgColor = substr(md5($user->email), 0, 6);
        return "https://ui-avatars.com/api/?name={$name}&background={$bgColor}&color=fff&size=128";
    }
}
