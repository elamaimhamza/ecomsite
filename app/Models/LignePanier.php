<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LignePanier extends Model
{
    protected $table = 'ligne_paniers';

    protected $fillable = ['panier_id', 'produit_id', 'quantite'];

    // Une ligne panier appartient à un panier
    public function panier()
    {
        return $this->belongsTo(Panier::class);
    }

    // Une ligne panier appartient à un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
