<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Redirection après connexion en fonction du rôle de l'utilisateur
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Vérifier si la requête attend une réponse JSON (API)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Vous êtes connecté avec succès',
                'user' => Auth::user()
            ]);
        }

        // Pour les requêtes web normales
        $user = Auth::user();

        if ($user->hasRole('super-admin')) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Redirection par défaut pour les utilisateurs sans rôle spécifique
        return view('welcome');
    }
}
