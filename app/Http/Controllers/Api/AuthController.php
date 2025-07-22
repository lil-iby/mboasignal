<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom_utilisateur' => 'required|string|max:255',
            'prenom_utilisateur' => 'required|string|max:255',
            'email_utilisateur' => 'required|string|email|max:255|unique:users',
            'pass_utilisateur' => 'required|string|min:6|confirmed',
            'tel_utilisateur' => 'required|string|max:20',
            'type_utilisateur' => 'required|string|in:admin,superadmin,citoyen,technicien',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'nom_utilisateur' => $request->nom_utilisateur,
            'prenom_utilisateur' => $request->prenom_utilisateur,
            'email_utilisateur' => $request->email_utilisateur,
            'pass_utilisateur' => Hash::make($request->pass_utilisateur),
            'tel_utilisateur' => $request->tel_utilisateur,
            'type_utilisateur' => $request->type_utilisateur,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email_utilisateur' => 'required|email',
            'pass_utilisateur' => 'required|string',
        ]);

        // Récupérer l'utilisateur par email
        $user = User::where('email_utilisateur', $request->email_utilisateur)->first();

        // Vérifier si l'utilisateur existe et que le mot de passe est correct
        if (!$user || !Hash::check($request->pass_utilisateur, $user->pass_utilisateur)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides',
                'errors' => [
                    'email_utilisateur' => ['Ces identifiants ne correspondent pas à nos enregistrements.']
                ]
            ], 401);
        }

        // Vérifier si le compte est activé
        if ($user->etat_compte !== 'activé') {
            return response()->json([
                'success' => false,
                'message' => 'Ce compte est désactivé',
                'errors' => [
                    'compte' => ['Votre compte a été désactivé. Veuillez contacter un administrateur.']
                ]
            ], 403);
        }

        // Authentifier l'utilisateur
        Auth::login($user);

        // Mettre à jour la date de dernière connexion
        $user->update([
            'derniere_connexion' => now(),
            'statut_en_ligne' => true
        ]);

        // Créer un nouveau token d'API pour l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Charger les relations nécessaires
        $user->load('organisme');
        
        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'user' => [
                    'id' => $user->id_utilisateur,
                    'nom' => $user->nom_utilisateur,
                    'prenom' => $user->prenom_utilisateur,
                    'email' => $user->email_utilisateur,
                    'telephone' => $user->tel_utilisateur,
                    'type_utilisateur' => $user->type_utilisateur,
                    'organisme' => $user->organisme ? [
                        'id' => $user->organisme->id_organisme,
                        'nom' => $user->organisme->nom_organisme,
                    ] : null,
                    'statut_en_ligne' => $user->statut_en_ligne,
                    'derniere_connexion' => $user->derniere_connexion,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('sanctum.expiration', 60 * 24 * 7) // en minutes
                ]
            ]
        ]);
    }

    /**
     * Récupère les informations de l'utilisateur connecté
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = Auth::user();
        $user->load('organisme');
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id_utilisateur,
                    'nom' => $user->nom_utilisateur,
                    'prenom' => $user->prenom_utilisateur,
                    'email' => $user->email_utilisateur,
                    'telephone' => $user->tel_utilisateur,
                    'type_utilisateur' => $user->type_utilisateur,
                    'organisme' => $user->organisme ? [
                        'id' => $user->organisme->id_organisme,
                        'nom' => $user->organisme->nom_organisme,
                    ] : null,
                    'statut_en_ligne' => $user->statut_en_ligne,
                    'derniere_connexion' => $user->derniere_connexion,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at
                ]
            ]
        ]);
    }

    /**
     * Déconnecte l'utilisateur et invalide le token actuel
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        
        // Mettre à jour le statut de l'utilisateur
        $user->update([
            'statut_en_ligne' => false
        ]);
        
        // Supprimer le token d'accès actuel
        $user->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Rafraîchit le token d'authentification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user = Auth::user();
        
        // Récupérer le token actuel
        $currentToken = $user->currentAccessToken();
        
        // Créer un nouveau token
        $token = $user->createToken('auth_token')->plainTextToken;
        
        // Supprimer l'ancien token
        $user->tokens()
            ->where('id', $currentToken->id)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Token rafraîchi avec succès',
            'data' => [
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('sanctum.expiration', 60 * 24 * 7) // en minutes
                ]
            ]
        ]);
    }
}
