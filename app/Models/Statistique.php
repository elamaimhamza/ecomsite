<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistique extends Model
{
    protected $table = 'statistiques';

    protected $fillable = [
        'nb_commandes',
        'chiffre_affaires',
        'produit_populaire_id',
    ];

    // Une statistique peut concerner un produit populaire (optionnel)
    public function produitPopulaire()
    {
        return $this->belongsTo(Produit::class, 'produit_populaire_id');
    }
}
