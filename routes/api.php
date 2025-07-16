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

    // route signalement non protégée
    Route::post('signalements', SignalementController::class);

    // Routes protégées par authentification
    Route::middleware('auth:api')->group(function () {
        // Utilisateurs
        Route::apiResource('utilisateurs', UtilisateurController::class);
        
        // Signalements
        Route::apiResource('signalements', SignalementController::class);
        
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
