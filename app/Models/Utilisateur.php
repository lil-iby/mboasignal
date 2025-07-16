<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Model
{
    use HasApiTokens;
    
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