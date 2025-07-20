<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Utilisateur;
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
            'email_utilisateur' => 'required|string|email|max:255|unique:utilisateurs',
            'pass_utilisateur' => 'required|string|min:6|confirmed',
            'tel_utilisateur' => 'required|string|max:20',
            'type_utilisateur' => 'required|string|in:utilisateur,admin',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Utilisateur::create([
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
            'password' => 'required|string',
        ]);

        // Récupérer l'utilisateur par email
        $user = \App\Models\Utilisateur::where('email_utilisateur', $request->email_utilisateur)->first();

        // Vérifier si l'utilisateur existe et que le mot de passe est correct
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->pass_utilisateur)) {
            // Créer un nouveau token pour l'utilisateur
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Mettre à jour la date de dernière connexion
            $user->update([
                'derniere_connexion' => now(),
                'statut_en_ligne' => true
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Connexion réussie',
                'data' => [
                    'user' => [
                        'id' => $user->id_utilisateur,
                        'nom' => $user->nom_utilisateur,
                        'prenom' => $user->prenom_utilisateur,
                        'email' => $user->email_utilisateur,
                        'type_utilisateur' => $user->type_utilisateur,
                        'statut_en_ligne' => $user->statut_en_ligne,
                        'derniere_connexion' => $user->derniere_connexion,
                    ],
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                        'expires_in' => config('sanctum.expiration', 60 * 24 * 7) // en minutes
                    ]
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'Identifiants invalides',
            'errors' => [
                'email_utilisateur' => ['Ces identifiants ne correspondent pas à nos enregistrements.']
            ]
        ], 422);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
