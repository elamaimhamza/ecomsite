<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';

    protected $fillable = ['utilisateur_id', 'montant_total', 'statut', 'stripe_session_id'];

    // Une commande appartient à un utilisateur
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    // Une commande contient plusieurs lignes commandes
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    // Une commande a un paiement
    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }

    // Une commande a une livraison
    public function livraison()
    {
        return $this->hasOne(Livraison::class);
    }
}
