<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visiteur extends Model
{
    protected $table = 'visiteurs';

    protected $fillable = [
        'id_utilisateur',
        'adresse_ip',
        'navigateur',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
