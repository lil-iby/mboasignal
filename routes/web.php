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
    ->name('logout');

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Tableau de bord admin
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Signalements admin
        Route::get('/signalements', function () {
            return view('admin.signalements');
        })->name('signalements');
        // Techniciens admin
        Route::get('/techniciens', function () {
            return view('admin.techniciens');
        })->name('techniciens');
        // Statistiques admin
        Route::get('/statistiques', function () {
            return view('admin.statistiques');
        })->name('statistiques');
        // Carte admin
        Route::get('/carte', function () {
            return view('admin.carte');
        })->name('carte');
        // Mon organisme admin
        Route::get('/mon_organisme', function () {
            return view('admin.mon_organisme');
        })->name('mon_organisme');
    });
Route::get('/unauthorized', function () {
    return view('unauthorized');
});

Route::prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        // Tableau de bord superadmin
        Route::get('/dashboard', function () {
            return view('superadmin.dashboard');
        })->name('dashboard');
        // Utilisateurs superadmin
        Route::get('/utilisateurs', function () {
            return view('superadmin.utilisateurs');
        })->name('utilisateurs');
        // Organismes superadmin
        Route::get('/organismes', function () {
            return view('superadmin.organismes');
        })->name('organismes');
        // Carte superadmin
        Route::get('/carte', function () {
            return view('superadmin.carte');
        })->name('carte');
        // Statistiques superadmin
        Route::get('/statistiques', function () {
            return view('superadmin.statistiques');
        })->name('statistiques');
        // Paramètres superadmin
        Route::get('/parametres', function () {
            return view('superadmin.parametres');
        })->name('parametres');
        // Ajoutez ici d'autres routes spécifiques au superadmin
    });
