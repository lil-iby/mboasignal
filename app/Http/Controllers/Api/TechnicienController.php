<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class TechnicienController extends Controller
{
    /**
     * Afficher la liste des techniciens
     */
    public function index()
    {
        $user = auth('api')->user();
        
        // Pour les administrateurs d'organisme, ne retourner que les techniciens de leur organisme
        if ($user->hasRole('admin_organisme')) {
            $techniciens = Utilisateur::whereHas('organisme', function($query) use ($user) {
                $query->where('id_organisme', $user->id_organisme);
            })
            ->where('type_utilisateur', 'technicien')
            ->with('organisme')
            ->get();
        } else {
            $techniciens = Utilisateur::where('type_utilisateur', 'technicien')
                ->with('organisme')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => $techniciens
        ]);
    }

    /**
     * Afficher un technicien spécifique
     */
    public function show($id)
    {
        $user = auth('api')->user();
        $technicien = Utilisateur::where('id_utilisateur', $id)
            ->where('type_utilisateur', 'technicien')
            ->with('organisme')
            ->firstOrFail();

        // Vérifier si l'utilisateur a le droit de voir ce technicien
        if ($user->hasRole('admin_organisme') && $technicien->id_organisme !== $user->id_organisme) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à ce technicien.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $technicien
        ]);
    }

    /**
     * Créer un nouveau technicien
     */
    public function store(Request $request)
    {
        $user = auth('api')->user();
        
        // Seul un admin d'organisme peut créer un technicien
        if (!$user->hasRole('admin_organisme')) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'required|string|max:50',
            'prenom_utilisateur' => 'required|string|max:50',
            'email_utilisateur' => 'required|string|email|max:100|unique:utilisateurs,email_utilisateur',
            'tel_utilisateur' => 'required|string|max:20',
            'pass_utilisateur' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $technicien = new Utilisateur();
            $technicien->nom_utilisateur = $request->nom_utilisateur;
            $technicien->prenom_utilisateur = $request->prenom_utilisateur;
            $technicien->email_utilisateur = $request->email_utilisateur;
            $technicien->tel_utilisateur = $request->tel_utilisateur;
            $technicien->pass_utilisateur = Hash::make($request->pass_utilisateur);
            $technicien->type_utilisateur = 'technicien';
            $technicien->id_organisme = $user->id_organisme;
            $technicien->statut_compte = 'actif';
            $technicien->save();

            // Attribuer le rôle de technicien
            $technicien->assignRole('technicien');

            return response()->json([
                'success' => true,
                'message' => 'Technicien créé avec succès',
                'data' => $technicien
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du technicien',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour un technicien
     */
    public function update(Request $request, $id)
    {
        $user = auth('api')->user();
        $technicien = Utilisateur::where('id_utilisateur', $id)
            ->where('type_utilisateur', 'technicien')
            ->firstOrFail();

        // Vérifier les autorisations
        if ($user->hasRole('admin_organisme') && $technicien->id_organisme !== $user->id_organisme) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'sometimes|required|string|max:50',
            'prenom_utilisateur' => 'sometimes|required|string|max:50',
            'email_utilisateur' => 'sometimes|required|string|email|max:100|unique:utilisateurs,email_utilisateur,' . $id . ',id_utilisateur',
            'tel_utilisateur' => 'sometimes|required|string|max:20',
            'pass_utilisateur' => 'sometimes|nullable|string|min:8|confirmed',
            'statut_compte' => 'sometimes|required|in:actif,inactif'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $technicien->fill($request->only([
                'nom_utilisateur', 
                'prenom_utilisateur', 
                'email_utilisateur',
                'tel_utilisateur',
                'statut_compte'
            ]));

            if ($request->filled('pass_utilisateur')) {
                $technicien->pass_utilisateur = Hash::make($request->pass_utilisateur);
            }

            $technicien->save();

            return response()->json([
                'success' => true,
                'message' => 'Technicien mis à jour avec succès',
                'data' => $technicien
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du technicien',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer un technicien
     */
    public function destroy($id)
    {
        $user = auth('api')->user();
        $technicien = Utilisateur::where('id_utilisateur', $id)
            ->where('type_utilisateur', 'technicien')
            ->firstOrFail();

        // Vérifier les autorisations
        if ($user->hasRole('admin_organisme') && $technicien->id_organisme !== $user->id_organisme) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        try {
            // Vérifier si le technicien a des signalements en cours
            $hasActiveSignalements = $technicien->signalements()
                ->whereIn('statut', ['en_attente', 'en_cours'])
                ->exists();

            if ($hasActiveSignalements) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer ce technicien car il a des signalements en cours.'
                ], 400);
            }

            $technicien->delete();

            return response()->json([
                'success' => true,
                'message' => 'Technicien supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du technicien',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les techniciens par organisme
     */
    public function byOrganisme($organismeId)
    {
        $user = auth('api')->user();
        
        // Vérifier si l'utilisateur a accès à cet organisme
        if ($user->hasRole('admin_organisme') && $user->id_organisme != $organismeId) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $techniciens = Utilisateur::where('id_organisme', $organismeId)
            ->where('type_utilisateur', 'technicien')
            ->where('statut_compte', 'actif')
            ->get(['id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur', 'email_utilisateur']);

        return response()->json([
            'success' => true,
            'data' => $techniciens
        ]);
    }

    /**
     * Obtenir les statistiques d'un technicien
     */
    public function stats($id)
    {
        $user = auth('api')->user();
        $technicien = Utilisateur::findOrFail($id);

        // Vérifier les autorisations
        if ($user->hasRole('admin_organisme') && $technicien->id_organisme !== $user->id_organisme) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ], 403);
        }

        $stats = [
            'total_signalements' => $technicien->signalements()->count(),
            'en_cours' => $technicien->signalements()->where('statut', 'en_cours')->count(),
            'resolus' => $technicien->signalements()->where('statut', 'resolu')->count(),
            'moyenne_temps_resolution' => $technicien->signalements()
                ->where('statut', 'resolu')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours'))
                ->value('avg_hours')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
