<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $medias = Media::all();
        return response()->json($medias);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fichier_media' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:5120',
            'type_media' => 'required|string',
            'signalement_id' => 'required|exists:signalements,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('fichier_media');
        $path = $file->store('public/medias');
        $url = Storage::url($path);

        $media = Media::create([
            'nom_media' => $file->getClientOriginalName(),
            'chemin_media' => $path,
            'type_media' => $request->type_media,
            'url_media' => $url,
            'signalement_id' => $request->signalement_id,
        ]);

        return response()->json($media, 201);
    }

    public function show($id)
    {
        $media = Media::findOrFail($id);
        return response()->json($media);
    }

    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        Storage::delete($media->chemin_media);
        $media->delete();
        return response()->json(null, 204);
    }
}
