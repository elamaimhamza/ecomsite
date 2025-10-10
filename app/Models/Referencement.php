<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referencement extends Model
{
      protected $table = 'referencements';

    protected $fillable = ['produit_id', 'mots_cles', 'description_seo', 'titre_seo'];

    // Un référencement appartient à un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
