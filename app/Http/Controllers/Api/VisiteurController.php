<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visiteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisiteurController extends Controller
{
    public function index()
    {
        $visiteurs = Visiteur::all();
        return response()->json($visiteurs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adresse_ip_visiteur' => 'required|ip',
            'user_agent_visiteur' => 'required|string',
            'date_visite' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $visiteur = Visiteur::create($request->all());
        return response()->json($visiteur, 201);
    }

    public function show($id)
    {
        $visiteur = Visiteur::findOrFail($id);
        return response()->json($visiteur);
    }

    public function destroy($id)
    {
        $visiteur = Visiteur::findOrFail($id);
        $visiteur->delete();
        return response()->json(null, 204);
    }
}
