<?php

namespace Database\Seeders;

use App\Models\TypeProduit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeProduitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
        $types = ['T-shirt basique', 'T-shirt imprimé', 'Polo', 'T-shirt de sport', 'T-shirt de luxe'];

        foreach ($types as $nom) {
            TypeProduit::create(['nom' => $nom]);
        }
    }
    }
}
