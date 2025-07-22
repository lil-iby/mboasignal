<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Signalement;
use App\Models\Utilisateur;

class Organisme extends Model
{
    protected $table = 'organismes';
    protected $primaryKey = 'id_organisme';

    protected $fillable = [
        'nom_organisme',
        'domaine_organisme',
        'email_organisme',
        'tel_organisme',
        'description_organisme',
        'adresse_organisme',
        'nombre_signalements',
        'statut_organisme',
    ];

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'id_organisme');
    }

    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'organisme_utilisateur', 'id_organisme', 'id_utilisateur');
    }
    public function parent()
    {
        return $this->belongsTo(Organisme::class, 'id_organisme_parent');
    }
    
    public function children()
    {
        return $this->hasMany(Organisme::class, 'id_organisme_parent');
    }

}

