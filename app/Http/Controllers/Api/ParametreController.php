<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class ParametreController extends Controller
{
    /**
     * Récupérer tous les paramètres
     */
    public function index()
    {
        $user = auth('api')->user();
        
        // Pour les administrateurs d'organisme, ne retourner que les paramètres pertinents
        if ($user->hasRole('admin_organisme')) {
            $parametres = Parametre::where('scope', 'organisme')
                ->orWhere('scope', 'global')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->cle => $item->valeur];
                });
        } 
        // Pour les super administrateurs, retourner tous les paramètres
        elseif ($user->hasRole('super_admin')) {
            $parametres = Parametre::all()->mapWithKeys(function ($item) {
                return [$item->cle => $item->valeur];
            });
        } 
        // Pour les autres utilisateurs, retourner uniquement les paramètres globaux
        else {
            $parametres = Parametre::where('scope', 'global')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->cle => $item->valeur];
                });
        }

        return response()->json([
            'success' => true,
            'data' => $parametres
        ]);
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $user = auth('api')->user();
        
        // Valider les données de la requête
        $validator = Validator::make($request->all(), [
            'params' => 'required|array',
            'params.*.cle' => 'required|string',
            'params.*.valeur' => 'required',
            'params.*.scope' => 'sometimes|in:global,organisme,utilisateur'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updated = [];
            
            foreach ($request->params as $param) {
                $parametre = Parametre::where('cle', $param['cle'])->first();
                
                // Vérifier les autorisations
                if ($parametre) {
                    // Un admin organisme ne peut modifier que les paramètres de son organisme ou globaux
                    if ($user->hasRole('admin_organisme') && $parametre->scope === 'utilisateur') {
                        continue;
                    }
                    
                    // Un utilisateur normal ne peut modifier que ses propres paramètres utilisateur
                    if (!$user->hasAnyRole(['admin_organisme', 'super_admin']) && $parametre->scope !== 'utilisateur') {
                        continue;
                    }
                    
                    // Mettre à jour la valeur
                    $parametre->valeur = $param['valeur'];
                    $parametre->save();
                    $updated[] = $parametre->cle;
                    
                    // Mettre à jour le cache
                    Cache::forget('parametre_' . $parametre->cle);
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($updated) . ' paramètre(s) mis à jour avec succès',
                'updated_params' => $updated
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des paramètres',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les catégories de signalement
     */
    public function categories()
    {
        $categories = Categorie::where('est_actif', true)
            ->orderBy('nom_categorie')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Créer une nouvelle catégorie
     */
    public function storeCategory(Request $request)
    {
        // Seul un super administrateur peut créer des catégories
        if (!auth('api')->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nom_categorie' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:50',
            'couleur' => 'nullable|string|size:7', // Format hexadécimal: #RRGGBB
            'est_actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categorie = Categorie::create([
                'nom_categorie' => $request->nom_categorie,
                'description_categorie' => $request->description,
                'icone_categorie' => $request->icone ?? 'fa-folder',
                'couleur_categorie' => $request->couleur ?? '#6c757d',
                'est_actif' => $request->boolean('est_actif', true)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catégorie créée avec succès',
                'data' => $categorie
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour une catégorie existante
     */
    public function updateCategory(Request $request, $id)
    {
        // Seul un super administrateur peut modifier les catégories
        if (!auth('api')->user()->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Action non autorisée.'
            ], 403);
        }

        $categorie = Categorie::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom_categorie' => 'sometimes|required|string|max:100|unique:categories,nom_categorie,' . $id . ',id_categorie',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:50',
            'couleur' => 'nullable|string|size:7',
            'est_actif' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categorie->fill($request->only([
                'nom_categorie', 'description_categorie', 'icone_categorie', 'couleur_categorie', 'est_actif'
            ]));
            
            // Si le nom est fourni, mettre à jour le slug
            if ($request->has('nom_categorie')) {
                $categorie->nom_categorie = $request->nom_categorie;
            }
            
            $categorie->save();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'data' => $categorie
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer un paramètre spécifique
     */
    public function getParam($key)
    {
        $parametre = Cache::rememberForever('parametre_' . $key, function () use ($key) {
            return Parametre::where('cle', $key)->first();
        });

        if (!$parametre) {
            return response()->json([
                'success' => false,
                'message' => 'Paramètre non trouvé'
            ], 404);
        }

        // Vérifier les autorisations en fonction du scope
        $user = auth('api')->user();
        
        if ($parametre->scope === 'organisme' && !$user->hasRole('admin_organisme')) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à ce paramètre'
            ], 403);
        }
        
        if ($parametre->scope === 'utilisateur' && $parametre->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à ce paramètre utilisateur'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'cle' => $parametre->cle,
                'valeur' => $parametre->valeur,
                'scope' => $parametre->scope,
                'description' => $parametre->description
            ]
        ]);
    }

    /**
     * Récupérer les paramètres de notification
     */
    public function notificationSettings()
    {
        $user = auth('api')->user();
        
        $settings = [
            'email_notifications' => $user->parametres()
                ->where('cle', 'like', 'notifications.email.%')
                ->pluck('valeur', 'cle')
                ->toArray(),
            'push_notifications' => $user->parametres()
                ->where('cle', 'like', 'notifications.push.%')
                ->pluck('valeur', 'cle')
                ->toArray(),
            'sms_notifications' => $user->parametres()
                ->where('cle', 'like', 'notifications.sms.%')
                ->pluck('valeur', 'cle')
                ->toArray(),
        ];

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Mettre à jour les paramètres de notification
     */
    public function updateNotificationSettings(Request $request)
    {
        $user = auth('api')->user();
        
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'sometimes|array',
            'email_notifications.*' => 'boolean',
            'push_notifications' => 'sometimes|array',
            'push_notifications.*' => 'boolean',
            'sms_notifications' => 'sometimes|array',
            'sms_notifications.*' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Mettre à jour les paramètres d'email
            if ($request->has('email_notifications')) {
                foreach ($request->email_notifications as $key => $value) {
                    $user->parametres()->updateOrCreate(
                        ['cle' => 'notifications.email.' . $key],
                        ['valeur' => $value, 'scope' => 'utilisateur']
                    );
                }
            }

            // Mettre à jour les paramètres de notifications push
            if ($request->has('push_notifications')) {
                foreach ($request->push_notifications as $key => $value) {
                    $user->parametres()->updateOrCreate(
                        ['cle' => 'notifications.push.' . $key],
                        ['valeur' => $value, 'scope' => 'utilisateur']
                    );
                }
            }

            // Mettre à jour les paramètres de SMS
            if ($request->has('sms_notifications')) {
                foreach ($request->sms_notifications as $key => $value) {
                    $user->parametres()->updateOrCreate(
                        ['cle' => 'notifications.sms.' . $key],
                        ['valeur' => $value, 'scope' => 'utilisateur']
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Paramètres de notification mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des paramètres de notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
