<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $table = 'produits';

    protected $fillable = [
        'nom', 'description', 'prix', 'image', 'marque', 'stock', 'genre_id', 'type_produit_id'
    ];

    // Un produit appartient à un genre
    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    // Un produit appartient à un type de produit
    public function typeProduit()
    {
        return $this->belongsTo(TypeProduit::class);
    }

    // Un produit peut apparaître dans plusieurs lignes panier
    public function lignePaniers()
    {
        return $this->hasMany(LignePanier::class);
    }

    // Un produit peut apparaître dans plusieurs lignes commande
    public function ligneCommandes()
    {
        return $this->hasMany(LigneCommande::class);
    }

    // Un produit a un référencement SEO
    public function referencement()
    {
        return $this->hasOne(Referencement::class);
    }
}
