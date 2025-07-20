<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Routes d'authentification personnalisées
Route::middleware('guest')->group(function () {
    // Page de connexion
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Page d'inscription
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Déconnexion (accessible uniquement aux utilisateurs connectés)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Groupe de routes protégées pour l'administration
Route::middleware(['auth', 'role:admin|super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Tableau de bord admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Ajoutez ici d'autres routes d'administration
    });

// Groupe de routes pour le super administrateur
Route::middleware(['auth', 'role:super-admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        // Tableau de bord super-admin
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        // Ajoutez ici d'autres routes spécifiques au super-admin
    });

// Redirection après connexion
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])
    ->name('home')
    ->middleware('auth');
