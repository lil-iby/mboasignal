<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'required|string|max:255',
            'prenom_utilisateur' => 'required|string|max:255',
            'email_utilisateur' => 'required|email|unique:utilisateurs,email_utilisateur',
            'pass_utilisateur' => 'required|min:6',
            'type_utilisateur' => 'required|string',
            'tel_utilisateur' => 'nullable|string|max:20',
            'photo_utilisateur' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $utilisateur = Utilisateur::create([
            'nom_utilisateur' => $request->nom_utilisateur,
            'prenom_utilisateur' => $request->prenom_utilisateur,
            'email_utilisateur' => $request->email_utilisateur,
            'pass_utilisateur' => Hash::make($request->pass_utilisateur),
            'type_utilisateur' => $request->type_utilisateur,
            'tel_utilisateur' => $request->tel_utilisateur,
            'photo_utilisateur' => $request->photo_utilisateur,
            'date_inscription' => now(),
            'etat_compte' => 'actif',
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès.',
            'utilisateur' => $utilisateur
        ], 201);
    }
}
