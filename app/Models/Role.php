<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Utilisateur as User;

class Role extends Model
{
    protected $fillable = [
        'name',
        'guard_name',
        'description'
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
