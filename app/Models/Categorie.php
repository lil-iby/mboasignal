<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_categorie';

    protected $fillable = [
        'nom_categorie',
        'description_categorie',
    ];

    public function signalements()
    {
        return $this->hasMany(Signalement::class, 'id_categorie');
    }
}
