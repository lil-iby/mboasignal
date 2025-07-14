<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public function medias()
    {
        return $this->hasMany(Media::class, 'id_signalement');
    }
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'id_categorie');
    }
    public function utilisateurs()
    {
        return $this->belongsToMany(Utilisateur::class, 'signalement_utilisateur', 'id_signalement', 'id_utilisateur');
    }

}
