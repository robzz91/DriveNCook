<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plat extends Model
{
    protected $table = 'plats';
    // on garde large et on filtre côté contrôleur
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function lignes()
    {
        return $this->hasMany(CommandeLigne::class, 'plat_id');
    }
}
