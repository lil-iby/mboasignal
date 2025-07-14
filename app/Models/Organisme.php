<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisme extends Model
{
    protected $table = 'organismes';
    protected $primaryKey = 'id_organisme';

    protected $fillable = [
        'nom_organisme',
        'contact_organisme',
        'adresse_organisme',
    ];

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'id_organisme');
    }

    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'organisme_utilisateur', 'id_organisme', 'id_utilisateur');
    }
    public function organisme()
    {
        return $this->belongsTo(Organisme::class, 'id_organisme');
    }

}

