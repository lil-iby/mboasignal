<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UtilisateurController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici vous pouvez enregistrer les routes de votre API. Elles seront
| automatiquement préfixées par "/api" grâce au middleware "api".
|
*/

Route::apiResource('utilisateurs', UtilisateurController::class);
Route::middleware('api')->get('/ping', function () {
    return response()->json(['message' => 'API fonctionne']);
});

Route::post('/v1/utilisateurs', [UtilisateurController::class, 'store']);
