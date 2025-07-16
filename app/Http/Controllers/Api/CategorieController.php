<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_categorie' => 'required|string|max:255|unique:categories',
            'description_categorie' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $categorie = Categorie::create($request->all());
        return response()->json($categorie, 201);
    }

    public function show($id)
    {
        $categorie = Categorie::findOrFail($id);
        return response()->json($categorie);
    }

    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom_categorie' => 'sometimes|string|max:255|unique:categories,nom_categorie,' . $id,
            'description_categorie' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $categorie->update($request->all());
        return response()->json($categorie);
    }

    public function destroy($id)
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->delete();
        return response()->json(null, 204);
    }
}
