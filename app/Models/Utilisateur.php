<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
     protected $table = 'utilisateurs';

    protected $fillable = [
        'nom', 'prenom', 'email', 'mot_de_passe', 'adresse', 'code_postal', 'ville', 'type_utilisateur'
    ];

    protected $hidden = ['mot_de_passe'];

    // Un utilisateur a un panier
    public function panier()
    {
        return $this->hasOne(Panier::class);
    }

    // Un utilisateur peut avoir plusieurs commandes
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }
}
