<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeLigne extends Model
{
    protected $table = 'commande_lignes';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function plat()
    {
        return $this->belongsTo(Plat::class, 'plat_id');
    }
}
