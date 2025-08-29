<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';

    protected $fillable = ['client_id','status','paye','total_ht','total_ttc'];

    public function lignes(){ return $this->hasMany(\App\Models\CommandeLigne::class,'commande_id'); }


    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
