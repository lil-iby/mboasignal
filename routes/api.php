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
| automatiquement préfixées par "/api/v1" grâce au middleware "api".
|
*/

// Route de test sans authentification
Route::get('/test', function () {
    return response()->json(['message' => 'API est opérationnelle']);
});

// Groupe de version v1 avec préfixe et middleware CORS
Route::prefix('v1')->group(function () {
    // Route de test pour v1
    Route::get('/test', function () {
        return response()->json(['message' => 'API v1 est opérationnelle']);
    });
    
    // Routes d'authentification publiques
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Tableau de bord
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'stats']);
        Route::get('/recent-activities', [DashboardController::class, 'recentActivities']);
        Route::get('/usage-stats', [DashboardController::class, 'usageStats']);
    });
        
        // Routes pour utilisateurs
        Route::apiResource('utilisateurs', UtilisateurController::class)->except(['store']);
        
        // Routes explicites pour chaque action du CRUD organisme
        Route::get('organismes', [OrganismeController::class, 'index'])->name('organismes.index');
        Route::post('organismes', [OrganismeController::class, 'store'])->name('organismes.store');
        Route::get('organismes/{id}', [OrganismeController::class, 'show'])->name('organismes.show');
        Route::put('organismes/{id}', [OrganismeController::class, 'update'])->name('organismes.update');
        Route::delete('organismes/{id}', [OrganismeController::class, 'destroy'])->name('organismes.destroy');
        // On garde la resource route pour compatibilité éventuelle
        Route::apiResource('organismes', OrganismeController::class);
        // Routes pour signalements
        Route::apiResource('signalements', SignalementController::class);
        
        // Routes d'authentification protégées
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        
        // Autres ressources
        Route::apiResource('categories', CategorieController::class);
        Route::apiResource('medias', MediaController::class);
        Route::apiResource('notifications', NotificationController::class);
        Route::apiResource('organismes', OrganismeController::class);
        Route::apiResource('visiteurs', VisiteurController::class);
        
        // Route de test
        Route::get('/ping', function () {
            return response()->json(['message' => 'API v1 fonctionne']);
        });
});
