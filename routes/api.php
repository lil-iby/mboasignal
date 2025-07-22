<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SignalementController;
use App\Http\Controllers\Api\UtilisateurController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrganismeController;
use App\Http\Controllers\Api\VisiteurController;
use App\Http\Controllers\Api\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici vous pouvez enregistrer les routes de votre API. Elles seront
| automatiquement préfixées par "/api" grâce au middleware "api".
|
*/

// Route de test sans authentification
Route::get('/test', function () {
    return response()->json(['message' => 'API est opérationnelle']);
});

// Groupe de version v1 avec préfixe
Route::prefix('v1')->group(function () {

    // Route de test pour v1 (publique)
    Route::get('/test', function () {
        return response()->json(['message' => 'API v1 est opérationnelle']);
    });

    // Routes d'authentification publiques
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Toutes les routes pour les signalements sont maintenant publiques
    Route::apiResource('signalements', SignalementController::class);
    Route::get('/signalements/stats/etat', [SignalementController::class, 'statsParEtat']);
    Route::get('/mes-signalements', [SignalementController::class, 'byOrganisme']);
    
    // Routes publiques pour les catégories
    Route::get('/categories', [CategorieController::class, 'index']);
    Route::post('/categories', [CategorieController::class, 'store']);

    // Routes protégées par authentification Sanctum
    Route::middleware(['auth:sanctum'])->group(function () {
        // Routes pour la session
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // Tableau de bord
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [DashboardController::class, 'stats']);
            Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
            Route::get('/usage-stats', [DashboardController::class, 'usageStats']);
        });

        // Autres ressources protégées
        Route::apiResource('utilisateurs', UtilisateurController::class);
        Route::apiResource('organismes', OrganismeController::class);
        Route::apiResource('categories', CategorieController::class);
        Route::apiResource('medias', MediaController::class);
        Route::apiResource('notifications', NotificationController::class);
        Route::apiResource('visiteurs', VisiteurController::class);

        // Route de test protégée
        Route::get('/ping', function () {
            return response()->json(['message' => 'API v1 fonctionne avec authentification']);
        });
    });
});
