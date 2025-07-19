<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $primaryKey = 'id_media';

    protected $fillable = [
        'fichier',
        'signalement_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function signalement()
    {
        return $this->belongsTo(Signalement::class, 'signalement_id');
    }
}
