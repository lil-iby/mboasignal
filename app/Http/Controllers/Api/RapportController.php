<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Utilisateur;
use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PDF;

class RapportController extends Controller
{
    /**
     * Récupérer la liste des rapports disponibles
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $reports = [];

        // Rapports disponibles pour tous les utilisateurs authentifiés
        $baseReports = [
            [
                'id' => 'my-reports',
                'name' => 'Mes rapports',
                'description' => 'Liste de tous les rapports que vous avez générés',
                'scope' => 'personal'
            ]
        ];

        // Rapports supplémentaires pour les administrateurs d'organisme
        if ($user->hasRole('admin_organisme')) {
            $baseReports = array_merge($baseReports, [
                [
                    'id' => 'technician-performance',
                    'name' => 'Performance des techniciens',
                    'description' => 'Évaluation des performances des techniciens de votre organisme',
                    'scope' => 'organisme'
                ],
                [
                    'id' => 'organisme-stats',
                    'name' => 'Statistiques de mon organisme',
                    'description' => 'Statistiques globales sur les signalements de votre organisme',
                    'scope' => 'organisme'
                ]
            ]);
        }

        // Rapports supplémentaires pour les super administrateurs
        if ($user->hasRole('super_admin')) {
            $baseReports = array_merge($baseReports, [
                [
                    'id' => 'global-stats',
                    'name' => 'Statistiques globales',
                    'description' => 'Vue d\'ensemble de toutes les activités sur la plateforme',
                    'scope' => 'global'
                ],
                [
                    'id' => 'organismes-comparison',
                    'name' => 'Comparaison des organismes',
                    'description' => 'Comparaison des performances entre les différents organismes',
                    'scope' => 'global'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $baseReports
        ]);
    }

    /**
     * Générer un rapport
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'format' => 'sometimes|in:json,pdf,excel',
            'filters' => 'sometimes|array'
        ]);

        $user = auth('api')->user();
        $reportType = $request->report_type;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $format = $request->format ?? 'json';

        // Vérifier les autorisations en fonction du type de rapport
        switch ($reportType) {
            case 'technician-performance':
                if (!$user->hasRole('admin_organisme')) {
                    return $this->unauthorizedResponse();
                }
                $data = $this->generateTechnicianPerformanceReport($user, $startDate, $endDate);
                break;

            case 'organisme-stats':
                if (!$user->hasRole('admin_organisme')) {
                    return $this->unauthorizedResponse();
                }
                $data = $this->generateOrganismeStatsReport($user, $startDate, $endDate);
                break;

            case 'global-stats':
            case 'organismes-comparison':
                if (!$user->hasRole('super_admin')) {
                    return $this->unauthorizedResponse();
                }
                $data = $this->generateGlobalReport($reportType, $startDate, $endDate);
                break;

            case 'my-reports':
            default:
                $data = $this->generateUserReport($user, $startDate, $endDate);
                break;
        }

        // Retourner le rapport dans le format demandé
        return $this->formatReport($data, $format, $reportType);
    }

    /**
     * Exporter un rapport
     */
    public function export(Request $request)
    {
        $request->merge(['format' => $request->format ?? 'pdf']);
        return $this->generate($request);
    }

    /**
     * Générer un rapport de performance des techniciens
     */
    private function generateTechnicianPerformanceReport($user, $startDate, $endDate)
    {
        $query = Utilisateur::where('id_organisme', $user->id_organisme)
            ->where('type_utilisateur', 'technicien')
            ->withCount(['signalements as total_signalements' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
            }])
            ->withCount(['signalements as signalements_resolus' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
                $q->where('statut', 'resolu');
            }])
            ->withCount(['signalements as signalements_en_cours' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
                $q->where('statut', 'en_cours');
            }])
            ->orderBy('nom_utilisateur');

        $techniciens = $query->get()->map(function($technicien) {
            return [
                'id' => $technicien->id_utilisateur,
                'nom_complet' => $technicien->prenom_utilisateur . ' ' . $technicien->nom_utilisateur,
                'email' => $technicien->email_utilisateur,
                'total_signalements' => $technicien->total_signalements,
                'signalements_resolus' => $technicien->signalements_resolus,
                'taux_resolution' => $technicien->total_signalements > 0 
                    ? round(($technicien->signalements_resolus / $technicien->total_signalements) * 100, 2) 
                    : 0,
                'signalements_en_cours' => $technicien->signalements_en_cours
            ];
        });

        return [
            'type' => 'technician-performance',
            'title' => 'Rapport de performance des techniciens',
            'period' => $this->getPeriodString($startDate, $endDate),
            'generated_at' => now()->toDateTimeString(),
            'data' => $techniciens,
            'summary' => [
                'total_techniciens' => $techniciens->count(),
                'total_signalements' => $techniciens->sum('total_signalements'),
                'taux_resolution_moyen' => $techniciens->avg('taux_resolution') ?? 0
            ]
        ];
    }

    /**
     * Générer un rapport statistique pour un organisme
     */
    private function generateOrganismeStatsReport($user, $startDate, $endDate)
    {
        $organisme = Organisme::findOrFail($user->id_organisme);
        
        // Statistiques de base
        $stats = [
            'total_signalements' => $organisme->signalements()->applyDateFilter($startDate, $endDate)->count(),
            'signalements_par_statut' => $organisme->signalements()
                ->select('statut', DB::raw('count(*) as total'))
                ->applyDateFilter($startDate, $endDate)
                ->groupBy('statut')
                ->pluck('total', 'statut'),
            'signalements_par_mois' => $this->getSignalementsByPeriod($organisme->id_organisme, $startDate, $endDate, 'month'),
            'categories_les_plus_signalées' => $organisme->signalements()
                ->select('categories.nom_categorie', DB::raw('count(*) as total'))
                ->join('categories', 'signalements.id_categorie', '=', 'categories.id_categorie')
                ->applyDateFilter($startDate, $endDate)
                ->groupBy('categories.nom_categorie')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get()
        ];

        return [
            'type' => 'organisme-stats',
            'title' => 'Statistiques de l\'organisme ' . $organisme->nom_organisme,
            'organisme' => $organisme->only(['id_organisme', 'nom_organisme', 'email_organisme']),
            'period' => $this->getPeriodString($startDate, $endDate),
            'generated_at' => now()->toDateTimeString(),
            'data' => $stats
        ];
    }

    /**
     * Générer un rapport global (pour les super administrateurs)
     */
    private function generateGlobalReport($reportType, $startDate, $endDate)
    {
        if ($reportType === 'global-stats') {
            $organismes = Organisme::withCount(['signalements' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
            }])->get();

            return [
                'type' => 'global-stats',
                'title' => 'Rapport statistique global',
                'period' => $this->getPeriodString($startDate, $endDate),
                'generated_at' => now()->toDateTimeString(),
                'data' => [
                    'total_organismes' => $organismes->count(),
                    'total_utilisateurs' => Utilisateur::count(),
                    'total_techniciens' => Utilisateur::where('type_utilisateur', 'technicien')->count(),
                    'total_signalements' => $organismes->sum('signalements_count'),
                    'signalements_par_organisme' => $organismes->map(function($org) {
                        return [
                            'organisme' => $org->nom_organisme,
                            'total_signalements' => $org->signalements_count
                        ];
                    }),
                    'signalements_par_mois' => $this->getSignalementsByPeriod(null, $startDate, $endDate, 'month')
                ]
            ];
        }

        // Comparaison des organismes
        $organismes = Organisme::withCount([
            'signalements as total_signalements' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
            },
            'signalements as signalements_resolus' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
                $q->where('statut', 'resolu');
            },
            'signalements as signalements_en_cours' => function($q) use ($startDate, $endDate) {
                $this->applyDateFilter($q, $startDate, $endDate);
                $q->where('statut', 'en_cours');
            }
        ])->having('total_signalements', '>', 0)
          ->orderBy('total_signalements', 'desc')
          ->get();

        return [
            'type' => 'organismes-comparison',
            'title' => 'Comparaison des organismes',
            'period' => $this->getPeriodString($startDate, $endDate),
            'generated_at' => now()->toDateTimeString(),
            'data' => $organismes->map(function($org) {
                return [
                    'organisme' => $org->nom_organisme,
                    'total_signalements' => $org->total_signalements,
                    'signalements_resolus' => $org->signalements_resolus,
                    'signalements_en_cours' => $org->signalements_en_cours,
                    'taux_resolution' => $org->total_signalements > 0 
                        ? round(($org->signalements_resolus / $org->total_signalements) * 100, 2) 
                        : 0
                ];
            })
        ];
    }

    /**
     * Générer un rapport personnel pour l'utilisateur
     */
    private function generateUserReport($user, $startDate, $endDate)
    {
        $query = $user->signalements();
        $this->applyDateFilter($query, $startDate, $endDate);
        
        $signalements = $query->with(['categorie', 'statut'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'type' => 'user-report',
            'title' => 'Mes signalements',
            'user' => $user->only(['id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur', 'email_utilisateur']),
            'period' => $this->getPeriodString($startDate, $endDate),
            'generated_at' => now()->toDateTimeString(),
            'data' => [
                'total_signalements' => $signalements->count(),
                'signalements_par_statut' => $signalements->groupBy('statut.nom_statut')->map->count(),
                'signalements_par_categorie' => $signalements->groupBy('categorie.nom_categorie')->map->count(),
                'signalements' => $signalements->map(function($signalement) {
                    return [
                        'id' => $signalement->id_signalement,
                        'titre' => $signalement->titre_signalement,
                        'description' => $signalement->description_signalement,
                        'statut' => $signalement->statut->nom_statut,
                        'categorie' => $signalement->categorie->nom_categorie,
                        'date_creation' => $signalement->created_at->format('d/m/Y H:i'),
                        'date_mise_a_jour' => $signalement->updated_at->format('d/m/Y H:i')
                    ];
                })
            ]
        ];
    }

    /**
     * Formater le rapport selon le format demandé
     */
    private function formatReport($data, $format, $reportType)
    {
        if ($format === 'json') {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }

        // Pour les formats PDF et Excel, on utilise des vues spécifiques
        $viewName = 'reports.' . str_replace('-', '_', $reportType);
        
        if ($format === 'pdf') {
            $pdf = PDF::loadView($viewName, $data);
            return $pdf->download('rapport-' . $reportType . '-' . now()->format('Y-m-d') . '.pdf');
        }

        // TODO: Implémenter l'export Excel si nécessaire
        return response()->json([
            'success' => false,
            'message' => 'Format non supporté pour le moment.'
        ], 400);
    }

    /**
     * Appliquer un filtre de date à une requête
     */
    private function applyDateFilter($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate->endOfDay());
        }
        return $query;
    }

    /**
     * Obtenir une chaîne de caractères représentant la période
     */
    private function getPeriodString($startDate, $endDate)
    {
        if (!$startDate && !$endDate) {
            return 'Toutes périodes';
        }
        
        $start = $startDate ? $startDate->format('d/m/Y') : 'Début';
        $end = $endDate ? $endDate->format('d/m/Y') : 'Aujourd\'hui';
        
        return 'Du ' . $start . ' au ' . $end;
    }

    /**
     * Obtenir les signalements groupés par période
     */
    private function getSignalementsByPeriod($organismeId = null, $startDate = null, $endDate = null, $period = 'month')
    {
        $query = Signalement::query();
        
        if ($organismeId) {
            $query->where('id_organisme', $organismeId);
        }
        
        $this->applyDateFilter($query, $startDate, $endDate);
        
        $format = $period === 'month' ? '%Y-%m' : '%Y-%m-%d';
        $interval = $period === 'month' ? '1 MONTH' : '1 DAY';
        
        return $query->select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as period"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('total', 'period');
    }

    /**
     * Réponse non autorisée
     */
    private function unauthorizedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Vous n\'êtes pas autorisé à accéder à ce rapport.'
        ], 403);
    }
}
