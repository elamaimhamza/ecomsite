<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $table = 'livraisons';

    protected $fillable = [
        'commande_id',
        'adresse_livraison',
        'mode_livraison',
        'date_expedition',
        'date_livraison',
        'transporteur_id',
        'numero_suivi'
    ];

    // Une livraison appartient à une commande
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function transporteur()
    {
        return $this->belongsTo(Transporteur::class);
    }
}
