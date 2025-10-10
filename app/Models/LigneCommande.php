<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
     protected $table = 'ligne_commandes';

    protected $fillable = ['commande_id', 'produit_id', 'quantite', 'prix_unitaire'];

    // Une ligne commande appartient à une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    // Une ligne commande appartient à un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
