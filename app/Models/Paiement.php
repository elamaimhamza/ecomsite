<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $table = 'paiements';

    protected $fillable = ['commande_id', 'type', 'montant', 'etat'];

    // Un paiement appartient à une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}
