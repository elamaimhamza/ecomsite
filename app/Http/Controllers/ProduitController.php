<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Produit;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the genre_id from request
        $genreName = $request->input('genre'); // or $request->input('genre_id');

        $genre =  Genre::where('nom', $genreName)->first();

        // Filter products by genre if genre_id is provided
        $produits = Produit::with(['genre', 'typeProduit'])
            ->when($genre, function ($query, $genre) {
                return $query->where('genre_id', $genre->id);
            })
            ->get();

        return response()->json([
            "message" => "fetched products",
            "data" => $produits,
            "Genre" => $genre
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produit = Produit::with(['genre', 'typeProduit', 'referencement'])->findOrFail($id);
        return response()->json($produit);
    }

    public function getProducts(Request $request)
    {
        $produitsList = $request->input('produitsIds');
        $produits = Produit::find($produitsList);
        return response()->json(["produits" => $produits]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produit $produit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $prix = $request->input("prix");
        if (is_null($prix)) {
            return response()->json(["message" => "prix est obligé"], 400);
        }
        $description = $request->input("description");
        $produit = Produit::find($id);
        $produit->update(["prix" => $prix, "description" => $description]);
        return response()->json(["message" => "product to update", "id" => $id, "produit" => $produit, $prix, $description]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produit $produit)
    {
        //
    }
}
