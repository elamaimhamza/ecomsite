<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transporteur extends Model
{
    protected $fillable = ['code', 'nom', 'details', 'prix'];

    public function livraisons()
    {
        return $this->hasMany(Livraison::class);
    }
}
