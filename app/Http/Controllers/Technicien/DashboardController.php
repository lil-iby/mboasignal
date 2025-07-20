<?php

namespace App\Http\Controllers\Technicien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $this->middleware('role:technicien');
    }

    /**
     * Afficher le tableau de bord du technicien.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les statistiques du technicien
        $stats = [
            'assignedTickets' => \App\Models\Ticket::where('technicien_id', $user->id)->count(),
            'pendingTickets' => \App\Models\Ticket::where('technicien_id', $user->id)
                                                 ->where('statut', 'en_cours')
                                                 ->count(),
            'resolvedTickets' => \App\Models\Ticket::where('technicien_id', $user->id)
                                                  ->where('statut', 'résolu')
                                                  ->count(),
            'averageResolutionTime' => $this->getAverageResolutionTime($user->id)
        ];

        // Récupérer les tickets récents
        $recentTickets = \App\Models\Ticket::where('technicien_id', $user->id)
                                          ->orderBy('created_at', 'desc')
                                          ->take(5)
                                          ->get();

        return view('technicien.dashboard.index', compact('stats', 'recentTickets'));
    }

    /**
     * Calculer le temps moyen de résolution des tickets pour le technicien.
     *
     * @param  int  $technicienId
     * @return string
     */
    private function getAverageResolutionTime($technicienId)
    {
        $tickets = \App\Models\Ticket::where('technicien_id', $technicienId)
                                    ->where('statut', 'résolu')
                                    ->whereNotNull('resolved_at')
                                    ->get();

        if ($tickets->isEmpty()) {
            return 'N/A';
        }

        $totalSeconds = $tickets->sum(function ($ticket) {
            return $ticket->created_at->diffInSeconds($ticket->resolved_at);
        });

        $averageSeconds = $totalSeconds / $tickets->count();
        
        return $this->formatDuration($averageSeconds);
    }

    /**
     * Formater la durée en secondes en un format lisible.
     *
     * @param  int  $seconds
     * @return string
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $remainingSeconds);
        }

        return sprintf('%ds', $remainingSeconds);
    }
}
