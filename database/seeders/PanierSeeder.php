<?php

namespace Database\Seeders;

use App\Models\Panier;
use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PanierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $utilisateur = Utilisateur::first();

        if ($utilisateur) {
            Panier::create([
                'utilisateur_id' => $utilisateur->id,
            ]);
    }}
}
