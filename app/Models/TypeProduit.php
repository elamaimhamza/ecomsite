<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeProduit extends Model
{
     protected $table = 'type_produits';

    protected $fillable = ['nom'];

    // Un type de produit a plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
