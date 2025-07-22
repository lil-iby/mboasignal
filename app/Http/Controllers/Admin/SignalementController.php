<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;

class SignalementController extends Controller
{
    /**
     * Affiche la liste des signalements
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les catégories pour les filtres
        $categories = Categorie::orderBy('nom_categorie')->get();
        
        return view('admin.signalements_list', compact('categories'));
    }
}
