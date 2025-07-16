<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SignalementController extends Controller
{
    public function index()
    {
        $signalements = Signalement::with(['utilisateur', 'categorie', 'medias'])->get();
        return response()->json($signalements);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre_signalement' => 'required|string|max:255',
            'description_signalement' => 'required|string',
            'localisation_signalement' => 'required|string',
            'date_signalement' => 'required|date',
            'etat_signalement' => 'required|string',
            'utilisateur_id' => 'required|exists:utilisateurs,id',
            'categorie_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Création du signalement
        $signalement = Signalement::create($request->except('images'));

        // Gestion du téléversement des images
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

        return response()->json($signalement, 201);
    }

    public function show($id)
    {
        $signalement = Signalement::with(['utilisateur', 'categorie', 'medias'])->findOrFail($id);
        return response()->json($signalement);
    }

    public function update(Request $request, $id)
    {
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
