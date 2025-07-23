<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Afficher le formulaire d'inscription
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Traiter la demande d'inscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
            'redirect' => route('login')
        ]);
    }

    /**
     * Valider les données de la requête
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nom_utilisateur' => ['required', 'string', 'max:50'],
            'prenom_utilisateur' => ['required', 'string', 'max:50'],
            'email_utilisateur' => ['required', 'string', 'email', 'max:100', 'unique:utilisateurs,email_utilisateur'],
            'tel_utilisateur' => ['required', 'string', 'max:20'],
            'pass_utilisateur' => ['required', 'string', 'min:8', 'confirmed'],
            'type_utilisateur' => ['required', 'string', 'in:utilisateur,admin'],
        ], [
            'email_utilisateur.unique' => 'Cette adresse email est déjà utilisée.',
            'pass_utilisateur.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'pass_utilisateur.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);
    }

    /**
     * Créer un nouvel utilisateur
     *
     * @param  array  $data
     * @return \App\Models\Utilisateur
     */
    protected function create(array $data)
    {
        $user = Utilisateur::create([
            'code_utilisateur' => $this->generateUniqueCode(),
            'nom_utilisateur' => $data['nom_utilisateur'],
            'prenom_utilisateur' => $data['prenom_utilisateur'],
            'email_utilisateur' => $data['email_utilisateur'],
            'tel_utilisateur' => $data['tel_utilisateur'],
            'pass_utilisateur' => Hash::make($data['pass_utilisateur']),
            'type_utilisateur' => $data['type_utilisateur'] ?? 'utilisateur',
            'statut_utilisateur' => 'activé',
        ]);

        // Attribuer le rôle correspondant au type d'utilisateur
        $roleName = $data['type_utilisateur'] ?? 'utilisateur';
        
        // Vérifier si le rôle existe, sinon le créer
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => $roleName],
            ['guard_name' => 'web']
        );
        
        $user->assignRole($role);
        
        return $user;
    }

    /**
     * Générer un code utilisateur unique
     *
     * @return string
     */
    protected function generateUniqueCode()
    {
        do {
            $code = 'USR' . strtoupper(Str::random(6));
        } while (Utilisateur::where('code_utilisateur', $code)->exists());

        return $code;
    }
}
