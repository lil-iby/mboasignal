<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SignalementController extends Controller
{
    /**
     * Récupère les statistiques des signalements par état
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statsParEtat()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }

        $stats = Signalement::selectRaw('etat_signalement, COUNT(*) as total')
            ->groupBy('etat_signalement')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->etat_signalement => $item->total];
            });

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    /**
     * Récupère la liste des signalements avec filtrage, tri et pagination
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Récupère la liste des signalements avec filtrage, tri et pagination
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié (optionnel selon les besoins)
        $user = auth('api')->user();
        
        // Initialiser la requête avec les relations
        $query = Signalement::with(['utilisateur' => function($q) {
            $q->select('id_utilisateur', 'nom_utilisateur', 'prenom_utilisateur', 'email_utilisateur');
        }, 'categorie', 'organisme', 'medias']);
        
        // Filtrage par état
        if ($request->has('etat')) {
            $query->where('etat_signalement', $request->etat);
        }
        
        // Filtrage par catégorie
        if ($request->has('categorie_id')) {
            $query->where('id_categorie', $request->categorie_id);
        }
        
        // Filtrage par organisme (pour les utilisateurs authentifiés avec un organisme)
        if ($user && $user->organisme_id) {
            $query->where('id_organisme', $user->organisme_id);
        } elseif ($request->has('organisme_id')) {
            // Permettre le filtrage par organisme_id si spécifié (pour les administrateurs)
            $query->where('id_organisme', $request->organisme_id);
        }
        
        // Filtrage par date
        if ($request->has('date_debut')) {
            $query->where('date_signalement', '>=', $request->date_debut);
        }
        
        if ($request->has('date_fin')) {
            $query->where('date_signalement', '<=', $request->date_fin . ' 23:59:59');
        }
        
        // Recherche par mot-clé
        if ($request->has('recherche')) {
            $searchTerm = '%' . $request->recherche . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nom_signalement', 'LIKE', $searchTerm)
                  ->orWhere('description_signalement', 'LIKE', $searchTerm)
                  ->orWhere('localisation_signalement', 'LIKE', $searchTerm);
            });
        }
        
        // Tri
        $sortField = $request->input('tri_champ', 'date_signalement');
        $sortOrder = $request->input('tri_ordre', 'desc');
        
        // Vérifier que le champ de tri est autorisé
        $allowedSortFields = ['date_signalement', 'date_enregistrement', 'etat_signalement', 'nom_signalement'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'date_signalement';
        }
        
        // Appliquer le tri
        $query->orderBy($sortField, $sortOrder === 'asc' ? 'asc' : 'desc');
        
        // Pagination
        $perPage = $request->input('par_page', 10);
        $perPage = min(max(1, $perPage), 100); // Limiter entre 1 et 100 éléments par page
        
        $signalements = $query->paginate($perPage);
        
        // Formater la réponse
        return response()->json([
            'success' => true,
            'data' => $signalements->items(),
            'pagination' => [
                'total' => $signalements->total(),
                'per_page' => $signalements->perPage(),
                'current_page' => $signalements->currentPage(),
                'last_page' => $signalements->lastPage(),
                'from' => $signalements->firstItem(),
                'to' => $signalements->lastItem(),
            ],
            'filters' => [
                'etat' => $request->etat,
                'categorie_id' => $request->categorie_id,
                'organisme_id' => $request->organisme_id,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'recherche' => $request->recherche,
                'tri_champ' => $sortField,
                'tri_ordre' => $sortOrder,
            ]
        ]);
    }
    
    /**
     * Récupère les signalements de l'organisme connecté
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function byOrganisme()
    {
        $user = auth('api')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }
        
        // Vérifier si l'utilisateur a un organisme associé
        if (!$user->organisme_id) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun organisme associé à cet utilisateur.'
            ], 403);
        }
        
        // Récupérer les signalements de l'organisme de l'utilisateur
        $signalements = Signalement::where('organisme_id', $user->organisme_id)
            ->with(['utilisateurs', 'categorie', 'medias'])
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $signalements
        ]);
        return response()->json($signalements);
    }

    /**
     * Enregistre un nouveau signalement
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Règles de validation
        $rules = [
            'titre_signalement' => 'required|string|max:255',
            'description_signalement' => 'required|string',
            'localisation_signalement' => 'required|string',
            'date_signalement' => 'required|date',
            'etat_signalement' => 'required|string|in:en_cours,traité,en_attente',
            'categorie_id' => 'required|exists:categories,id_categorie',
            'organisme_id' => 'nullable|exists:organismes,id_organisme',
            'fichiers' => 'nullable|array',
            'fichiers.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // 10MB max par fichier
            'token' => 'nullable|string', // Jeton optionnel
        ];

        // Valider les données
        $validator = Validator::make($request->all(), $rules, [
            'fichiers.*.max' => 'Chaque fichier ne doit pas dépasser 10MB',
            'fichiers.*.mimes' => 'Les types de fichiers autorisés sont : jpg, jpeg, png, pdf, doc, docx',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérifier l'authentification si un token est fourni
        $user = null;
        if ($request->bearerToken()) {
            $user = auth('api')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token invalide ou expiré.'
                ], 401);
            }
        }

        // Préparer les données du signalement
        $data = [
            'nom_signalement' => $request->titre_signalement,
            'description_signalement' => $request->description_signalement,
            'localisation_signalement' => $request->localisation_signalement,
            'date_enregistrement' => now(),
            'etat_signalement' => $request->etat_signalement,
            'statut_signalement' => 'nouveau',
            'id_categorie' => $request->categorie_id,
            'id_utilisateur' => $user ? $user->id_utilisateur : null,
            'id_organisme' => $request->organisme_id ?? ($user ? $user->organisme_id : null),
            'date_signalement' => $request->date_signalement,
            'token' => $request->token, // Stocker le jeton s'il est fourni
        ];
        
        // Démarrer une transaction pour s'assurer que tout se passe bien
        DB::beginTransaction();
        
        try {
            // Création du signalement
            $signalement = Signalement::create($data);
            
            // Attacher l'utilisateur authentifié au signalement
            if ($user) {
                $signalement->utilisateurs()->attach($user->id_utilisateur);
            }

            // Gestion du téléversement des fichiers
            if ($request->hasFile('fichiers')) {
                foreach ($request->file('fichiers') as $file) {
                    // Créer un nom de fichier unique
                    $extension = $file->getClientOriginalExtension();
                    $nomFichier = 'signalement_' . $signalement->id_signalement . '_' . uniqid() . '.' . $extension;
                    
                    // Stocker le fichier
                    $chemin = $file->storeAs('public/signalements', $nomFichier);
                    $url = Storage::url($chemin);
                    
                    // Déterminer le type de média
                    $typeMedia = $this->determinerTypeMedia($extension);
                    
                    // Créer l'entrée média
                    $media = new Media([
                        'nom_media' => $file->getClientOriginalName(),
                        'chemin_media' => $chemin,
                        'url_media' => $url,
                        'type_media' => $typeMedia,
                        'taille_media' => $file->getSize(),
                    ]);
                    
                    $signalement->medias()->save($media);
                }
            }
            
            // Gestion des fichiers en base64
            if ($request->has('fichiers_base64') && is_array($request->fichiers_base64)) {
                foreach ($request->fichiers_base64 as $fileData) {
                    if (isset($fileData['contenu_base64']) && isset($fileData['nom_fichier'])) {
                        $filePath = $this->saveBase64File($fileData['contenu_base64'], $fileData['nom_fichier']);
                        
                        $media = new Media([
                            'nom_media' => $fileData['nom_fichier'],
                            'chemin_media' => $filePath,
                            'type_media' => mime_content_type($filePath),
                            'taille_media' => filesize($filePath),
                        ]);
                        
                        $signalement->medias()->save($media);
                    }
                }
            }

            // Valider la transaction
            DB::commit();
            
            // Recharger le signalement avec les médias
            $signalement->load('medias');

            return response()->json([
                'message' => 'Signalement créé avec succès',
                'data' => $signalement
            ], 201);
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            
            // Supprimer les fichiers téléversés en cas d'échec
            if (isset($chemin) && Storage::exists($chemin)) {
                Storage::delete($chemin);
            }
            
            return response()->json([
                'message' => 'Une erreur est survenue lors de la création du signalement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistre un fichier encodé en base64
     *
     * @param string $base64String
     * @param string $fileName
     * @return string Chemin du fichier enregistré
     */
    private function saveBase64File($base64String, $fileName)
    {
        // Décoder le contenu base64
        $fileData = base64_decode(preg_replace('#^data:\w+/\w+;base64,#i', '', $base64String));
        
        // Créer un nom de fichier unique
        $uniqueName = uniqid() . '_' . $fileName;
        $path = 'public/signalements/' . $uniqueName;
        
        // Enregistrer le fichier
        Storage::put($path, $fileData);
        
        return storage_path('app/' . $path);
    }

    /**
     * Détermine le type de média en fonction de l'extension du fichier
     *
     * @param string $extension
     * @return string
     */
    private function determinerTypeMedia($extension)
    {
        $extension = strtolower($extension);
        
        // Types d'images
        $images = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
        if (in_array($extension, $images)) {
            return 'image';
        }
        
        // Types de vidéos
        $videos = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'];
        if (in_array($extension, $videos)) {
            return 'video';
        }
        
        // Types de documents
        $documents = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods'];
        if (in_array($extension, $documents)) {
            return 'document';
        }
        
        // Types d'archives
        $archives = ['zip', 'rar', '7z', 'tar', 'gz'];
        if (in_array($extension, $archives)) {
            return 'archive';
        }
        
        // Par défaut, retourner 'fichier'
        return 'fichier';
    }
    
    public function show($id)
    {
        $signalement = Signalement::with(['utilisateurs', 'categorie', 'medias'])->findOrFail($id);
        return response()->json($signalement);
    }

    public function update(Request $request, $id)
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }
        
        $signalement = Signalement::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'titre_signalement' => 'sometimes|string|max:255',
            'description_signalement' => 'sometimes|string',
            'localisation_signalement' => 'sometimes|string',
            'etat_signalement' => 'sometimes|string',
            'categorie_id' => 'sometimes|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Démarrer une transaction
        DB::beginTransaction();
        
        try {
            // Mise à jour du signalement
            $signalement->update($request->except(['images', 'utilisateurs']));
            
            // Mise à jour des utilisateurs associés si fournis
            if ($request->has('utilisateurs')) {
                $signalement->utilisateurs()->sync($request->utilisateurs);
            }

            // Gestion de l'ajout de nouvelles images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('public/signalements');
                    $url = Storage::url($path);

                    Media::create([
                        'nom_media' => $image->getClientOriginalName(),
                        'chemin_media' => $path,
                        'url_media' => $url,
                        'type_media' => 'image',
                        'signalement_id' => $signalement->id_signalement,
                    ]);
                }
            }
            
            // Valider la transaction
            DB::commit();
            
            // Recharger le signalement avec les relations
            $signalement->load(['utilisateurs', 'categorie', 'medias']);
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour du signalement.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json($signalement);
    }

    public function destroy($id)
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }
        
        // Démarrer une transaction
        DB::beginTransaction();
        
        try {
            $signalement = Signalement::with(['utilisateurs', 'medias'])->findOrFail($id);
            
            // Détacher tous les utilisateurs associés
            $signalement->utilisateurs()->detach();
            
            // Supprimer les fichiers associés
            foreach ($signalement->medias as $media) {
                Storage::delete($media->chemin_media);
            }
            
            // Supprimer le signalement
            $signalement->delete();
            
            // Valider la transaction
            DB::commit();
            
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du signalement.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
