<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Utilisateur::create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'mot_de_passe' => Hash::make('motdepasse123'),
            'adresse' => '10 rue des Lilas',
            'code_postal' => '75000',
            'ville' => 'Paris',
            'type_utilisateur' => 'Visiteur',
        ]);

        Utilisateur::create([
            'nom' => 'Martin',
            'prenom' => 'Sophie',
            'email' => 'sophie.martin@example.com',
            'mot_de_passe' => Hash::make('motdepasse123'),
            'adresse' => '15 avenue des Champs',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'type_utilisateur' => 'Visiteur',
        ]);
    }
}
