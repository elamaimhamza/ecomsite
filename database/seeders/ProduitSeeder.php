<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Produit;
use App\Models\TypeProduit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les genres
        $genreHomme = Genre::where('nom', 'Homme')->first();
        $genreFemme = Genre::where('nom', 'Femme')->first();
        $genreEnfant = Genre::where('nom', 'Enfant')->first();

        // Récupérer les types de produits
        $tshirtBasique = TypeProduit::where('nom', 'T-shirt basique')->first();
        $polo = TypeProduit::where('nom', 'Polo')->first();

        // Exemple de produits
        Produit::create([
            'nom' => 'T-shirt basique noir homme',
            'description' => 'T-shirt basique noir, coton 100%',
            'prix' => 15.99,
            'image' => 'tshirt_basique_noir_homme.jpg',
            'marque' => 'MarqueA',
            'stock' => 100,
            'genre_id' => $genreHomme->id,
            'type_produit_id' => $tshirtBasique->id,
        ]);

        Produit::create([
            'nom' => 'Polo bleu femme',
            'description' => 'Polo bleu élégant pour femme',
            'prix' => 29.99,
            'image' => 'polo_bleu_femme.jpg',
            'marque' => 'MarqueB',
            'stock' => 50,
            'genre_id' => $genreFemme->id,
            'type_produit_id' => $polo->id,
        ]);
    }
}
