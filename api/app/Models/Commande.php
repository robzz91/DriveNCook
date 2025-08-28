<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function lignes()
    {
        return $this->hasMany(CommandeLigne::class, 'commande_id');
    }
}
