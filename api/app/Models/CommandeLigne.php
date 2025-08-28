<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandeLigne extends Model
{
    protected $table = 'commande_lignes';

    protected $fillable = ['commande_id','plat_id','quantite','prix_unitaire','total_ligne'];


    public function commande(){ return $this->belongsTo(\App\Models\Commande::class,'commande_id'); }

}
