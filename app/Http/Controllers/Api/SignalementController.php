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
    public function index()
    {
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }
        
        $signalements = Signalement::with(['utilisateur', 'categorie', 'medias'])->get();
        return response()->json($signalements);
    }

    public function store(Request $request)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentification requise. Token manquant ou invalide.'
            ], 401);
        }

        // Vérifier si la requête est en JSON
        $isJsonRequest = $request->isJson();
        $jsonData = [];
        $uploadedFiles = [];

        if ($isJsonRequest) {
            $jsonData = $request->json()->all();
            $request->merge($jsonData);
            
            // Si des fichiers sont envoyés en base64 dans le JSON
            if (isset($jsonData['fichiers']) && is_array($jsonData['fichiers'])) {
                foreach ($jsonData['fichiers'] as $index => $fileData) {
                    if (isset($fileData['contenu_base64']) && isset($fileData['nom_fichier'])) {
                        $filePath = $this->saveBase64File($fileData['contenu_base64'], $fileData['nom_fichiler']);
                        $uploadedFiles[] = new \Illuminate\Http\UploadedFile(
                            $filePath,
                            $fileData['nom_fichier'],
                            mime_content_type($filePath),
                            null,
                            true
                        );
                    }
                }
                $request->files->set('fichiers', $uploadedFiles);
            }
        }

        // Règles de validation de base
        $rules = [
            'titre_signalement' => 'required|string|max:255',
            'description_signalement' => 'required|string',
            'localisation_signalement' => 'required|string',
            'date_signalement' => 'required|date',
            'etat_signalement' => 'required|string',
            'utilisateur_id' => 'nullable|exists:utilisateurs,id_utilisateur',
            'categorie_id' => 'required|exists:categories,id_categorie',
            'id_organisme' => 'nullable|exists:organismes,id_organisme',
        ];

        // Ajouter les règles pour les fichiers uniquement s'ils sont présents
        if ($request->has('fichiers') || $request->hasFile('fichiers')) {
            $rules['fichiers'] = 'array';
            $rules['fichiers.*'] = [
                function ($attribute, $value, $fail) {
                    if ($value !== null && 
                        !($value instanceof \Illuminate\Http\UploadedFile) && 
                        !is_string($value)) {
                        $fail('Le fichier doit être un fichier valide ou une chaîne base64.');
                    }
                },
                'max:10240' // 10MB max par fichier
            ];
        }

        $validator = Validator::make($request->all(), $rules, [
            'fichiers.*.max' => 'Chaque fichier ne doit pas dépasser 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            'id_organisme' => $request->id_organisme,
            'date_signalement' => $request->date_signalement,
        ];
        
        // Si l'utilisateur est authentifié, utiliser son ID
        if (auth('api')->check()) {
            $data['utilisateur_id'] = auth('api')->id();
        }
        
        // Démarrer une transaction pour s'assurer que tout se passe bien
        DB::beginTransaction();
        
        try {
            // Création du signalement
            $signalement = Signalement::create($data);

            // Gestion du téléversement des fichiers
            if ($request->hasFile('fichiers')) {
                foreach ($request->file('fichiers') as $fichier) {
                    $extension = $fichier->getClientOriginalExtension();
                    $nomFichier = 'signalement_' . $signalement->id . '_' . uniqid() . '.' . $extension;
                    
                    // Stocker le fichier dans le dossier public/signalements
                    $chemin = $fichier->storeAs('public/signalements', $nomFichier);
                    $url = Storage::url($chemin);
                    
                    // Déterminer le type de média en fonction de l'extension
                    $typeMedia = $this->determinerTypeMedia($extension);

                    Media::create([
                        'fichier' => $nomFichier,
                        'nom_media' => $fichier->getClientOriginalName(),
                        'chemin_media' => $chemin,
                        'url_media' => $url,
                        'type_media' => $typeMedia,
                        'signalement_id' => $signalement->id_signalement,
                    ]);
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
        $signalement = Signalement::with(['utilisateur', 'categorie', 'medias'])->findOrFail($id);
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

        // Mise à jour du signalement
        $signalement->update($request->except('images'));

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
                    'signalement_id' => $signalement->id,
                ]);
            }
        }

        // Recharger le signalement avec les médias
        $signalement->load('medias');

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
        
        $signalement = Signalement::with('medias')->findOrFail($id);
        
        // Supprimer les fichiers associés
        foreach ($signalement->medias as $media) {
            Storage::delete($media->chemin_media);
        }
        
        // Supprimer le signalement (les médias seront supprimés en cascade si la relation est bien configurée)
        $signalement->delete();
        
        return response()->json(null, 204);
    }
}
