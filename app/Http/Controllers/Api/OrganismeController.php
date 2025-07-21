<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrganismeController extends Controller
{
    public function index()
    {
        $organismes = Organisme::all();
        return response()->json($organismes);
    }

    public function store(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user || $user->type_utilisateur !== 'superadmin') {
            return response()->json(['message' => 'Action réservée aux super administrateurs.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'nom_organisme' => 'required|string|max:255|unique:organismes',
            'adresse_organisme' => 'required|string',
            'tel_organisme' => 'required|string|max:20',
            'email_organisme' => 'required|email|unique:organismes',
            'description_organisme' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['nombre_signalements'] = 0;
        $organisme = Organisme::create($data);
        return response()->json($organisme, 201);
    }

    public function show($id)
    {
        $organisme = Organisme::findOrFail($id);
        return response()->json($organisme);
    }

    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user || $user->type_utilisateur !== 'superadmin') {
            return response()->json(['message' => 'Action réservée aux super administrateurs.'], 403);
        }
        $organisme = Organisme::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom_organisme' => 'sometimes|string|max:255|unique:organismes,nom_organisme,' . $id,
            'adresse_organisme' => 'sometimes|string',
            'tel_organisme' => 'sometimes|string|max:20',
            'email_organisme' => 'sometimes|email|unique:organismes,email_organisme,' . $id,
            'description_organisme' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $organisme->update($request->all());
        return response()->json($organisme);
    }

    public function destroy($id)
    {
        $user = auth('sanctum')->user();
        if (!$user || $user->type_utilisateur !== 'superadmin') {
            return response()->json(['message' => 'Action réservée aux super administrateurs.'], 403);
        }
        $organisme = Organisme::findOrFail($id);
        $organisme->delete();
        return response()->json(null, 204);
    }
}
