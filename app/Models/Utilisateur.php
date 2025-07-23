<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Organisme;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use \App\Traits\AuthenticatableTrait;
    
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'code_utilisateur',
        'nom_utilisateur',
        'prenom_utilisateur',
        'email_utilisateur',
        'pass_utilisateur',
        'type_utilisateur',
        'tel_utilisateur',
        'tokenid',
        'day_token',
        'hour_token',
        'etat_compte',
        'type_compte',
        'date_inscription',
        'date_confirmation',
        'date_suppression',
        'derniere_modification',
        'statut_en_ligne',
        'photo_utilisateur',
        'statut_utilisateur',
    ];
    
    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pass_utilisateur',
        'remember_token',
    ];

    /**
     * Get the first organisme of the user.
     */
    public function organisme()
    {
        return $this->organismes()->first();
    }
    
    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_inscription' => 'datetime',
        'date_confirmation' => 'datetime',
        'date_suppression' => 'datetime',
        'derniere_modification' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'date_inscription';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'derniere_modification';

    // Les méthodes d'authentification sont maintenant dans le trait AuthenticatableTrait

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id_utilisateur';
    }




    public function visiteur()
    {
        return $this->hasOne(Visiteur::class, 'id_utilisateur');
    }
    public function signalements()
    {
        return $this->belongsToMany(Signalement::class, 'signalement_utilisateur', 'id_utilisateur', 'id_signalement');
    }
    public function organismes()
    {
        return $this->belongsToMany(Organisme::class, 'organisme_utilisateur', 'id_utilisateur', 'id_organisme');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_utilisateur');
    }

}