<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Nom de la table
    protected $table = 'clients';

    // Colonnes modifiables
    protected $fillable = [
        'nom',
        'email',
        'password',
    ];

    // Cacher le mot de passe dans les retours JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
