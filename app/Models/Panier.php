<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    protected $table = 'paniers';

    protected $fillable = ['utilisateur_id'];

    // Un panier appartient à un utilisateur (peut être null pour visiteur anonyme)
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    // Un panier contient plusieurs lignes panier
    public function lignePaniers()
    {
        return $this->hasMany(LignePanier::class);
    }
}
