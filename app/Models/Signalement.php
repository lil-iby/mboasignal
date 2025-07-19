<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Organisme;

class Signalement extends Model
{
    protected $table = 'signalements';
    protected $primaryKey = 'id_signalement';

    protected $fillable = [
        'nom_signalement',
        'description_signalement',
        'date_enregistrement',
        'latitude',
        'longitude',
        'etat_signalement',
        'date_modification',
        'statut_signalement',
        'id_categorie',
        'id_organisme',
        'utilisateur_id'
    ];
    
    protected $dates = [
        'date_enregistrement',
        'date_modification',
        'created_at',
        'updated_at'
    ];

    public function medias()
    {
        return $this->hasMany(Media::class, 'signalement_id');
    }
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }
    
    public function organisme()
    {
        return $this->belongsTo(Organisme::class, 'id_organisme', 'id_organisme');
    }
    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'signalement_utilisateur', 'id_signalement', 'id_utilisateur');
    }

}
