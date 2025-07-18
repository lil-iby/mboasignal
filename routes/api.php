<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UtilisateurController;
use App\Http\Controllers\Api\SignalementController;
use App\Http\Controllers\Api\CategorieController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrganismeController;
use App\Http\Controllers\Api\VisiteurController;

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

// Groupe de version v1
Route::prefix('v1')->group(function () {
    // Route de test pour v1
    Route::get('/test', function () {
        return response()->json(['message' => 'API v1 est opérationnelle']);
    });
    
    // Routes d'authentification
    Route::post('/login', [UtilisateurController::class, 'login']);
    Route::post('/register', [UtilisateurController::class, 'register']);
    Route::post('/refresh', [UtilisateurController::class, 'refresh']);

    // Routes publiques (sans authentification)
    Route::post('/signalements', [SignalementController::class, 'store']);

    // Routes protégées par authentification JWT
    Route::middleware('jwt.verify')->group(function () {
        // Utilisateurs
        Route::apiResource('utilisateurs', UtilisateurController::class)->except(['store']);
        
        // Routes pour signalements (toutes sauf store qui est déjà défini)
        Route::get('signalements', [SignalementController::class, 'index']);
        Route::get('signalements/{id}', [SignalementController::class, 'show']);
        Route::put('signalements/{id}', [SignalementController::class, 'update']);
        Route::patch('signalements/{id}', [SignalementController::class, 'update']);
        Route::delete('signalements/{id}', [SignalementController::class, 'destroy']);
        
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
});
