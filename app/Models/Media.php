<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id_media';

    protected $fillable = [
        'fichier',
        'id_signalement',
    ];

    public function signalement()
    {
        return $this->belongsTo(Signalement::class, 'id_signalement');
    }
}
