<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UtilisateurController extends Controller
{
    /**
     * Créer un nouvel utilisateur (API REST)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'required|string|max:255',
            'prenom_utilisateur' => 'required|string|max:255',
            'email_utilisateur' => 'required|email|unique:utilisateurs,email_utilisateur',
            'pass_utilisateur' => 'required|min:6|confirmed',
            'type_utilisateur' => 'required|string|in:citoyen,technicien,administrateur,superadmin',
            'tel_utilisateur' => 'nullable|string|max:20',
            'photo_utilisateur' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            'etat_compte' => 'activé',
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'utilisateur' => $utilisateur
        ], 201);
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'required|string|max:255',
            'prenom_utilisateur' => 'required|string|max:255',
            'email_utilisateur' => 'required|email|unique:utilisateurs,email_utilisateur',
            'pass_utilisateur' => 'required|min:6|confirmed',
            'type_utilisateur' => 'required|string|in:admin,utilisateur,moderateur',
            'tel_utilisateur' => 'nullable|string|max:20',
            'photo_utilisateur' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
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
            'etat_compte' => 'activé',
        ]);

        $token = JWTAuth::fromUser($utilisateur);

        return response()->json([
            'message' => 'Utilisateur enregistré avec succès',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'utilisateur' => $utilisateur
        ], 201);
    }

    /**
     * Connecter un utilisateur
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email_utilisateur', 'pass_utilisateur');
        
        if (!$token = auth('api')->attempt(['email_utilisateur' => $credentials['email_utilisateur'], 'password' => $credentials['pass_utilisateur']])) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        $utilisateur = auth('api')->user();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'utilisateur' => $utilisateur
        ]);
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    /**
     * Afficher le profil de l'utilisateur connecté
     */
    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Rafraîchir un token JWT expiré
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Obtenir la structure du tableau de la réponse du token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }

    /**
     * Afficher la liste des utilisateurs
     */
    public function index()
    {
        $utilisateurs = Utilisateur::all();
        return response()->json($utilisateurs);
    }

    /**
     * Afficher un utilisateur spécifique
     */
    public function show($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        return response()->json($utilisateur);
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'sometimes|string|max:255',
            'prenom_utilisateur' => 'sometimes|string|max:255',
            'email_utilisateur' => 'sometimes|email|unique:utilisateurs,email_utilisateur,' . $id,
            'pass_utilisateur' => 'sometimes|min:6',
            'type_utilisateur' => 'sometimes|string|in:admin,utilisateur,moderateur',
            'tel_utilisateur' => 'nullable|string|max:20',
            'photo_utilisateur' => 'nullable|string|max:255',
            'etat_compte' => 'sometimes|in:activé,désactivé,suspendu'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        if (isset($data['pass_utilisateur'])) {
            $data['pass_utilisateur'] = Hash::make($data['pass_utilisateur']);
        }

        $utilisateur->update($data);
        return response()->json($utilisateur);
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->delete();
        return response()->json(null, 204);
    }
}
