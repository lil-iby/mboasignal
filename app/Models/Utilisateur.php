<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable implements JWTSubject
{
    use Notifiable;
    
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
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

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id_utilisateur';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->pass_utilisateur;
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