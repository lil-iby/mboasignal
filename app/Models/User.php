<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Organisme;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'utilisateurs';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_utilisateur';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom_utilisateur',
        'prenom_utilisateur',
        'email_utilisateur',
        'pass_utilisateur',
        'type_utilisateur',
        'tel_utilisateur',
        'etat_compte',
        'photo_utilisateur'
    ];

    /**
     * Get the organismes for the user.
     */
    public function organismes()
    {
        return $this->belongsToMany(Organisme::class, 'organisme_utilisateur', 'id_utilisateur', 'id_organisme');
    }

    /**
     * Get the first organisme of the user.
     */
    public function organisme()
    {
        return $this->organismes()->first();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'pass_utilisateur',
        'remember_token',
        'tokenid',
        'day_token',
        'hour_token'
    ];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->pass_utilisateur;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id_utilisateur';
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'pass_utilisateur' => 'hashed',
        'statut_en_ligne' => 'boolean',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email_utilisateur;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email_utilisateur;
    }
}
