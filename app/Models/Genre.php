<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
       protected $table = 'genres';

    protected $fillable = ['nom'];

    // Un genre a plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
