<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Produit;
use Illuminate\Http\Request;

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
    public function show(Produit $produit)
    {
        //
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
    public function update(Request $request, Produit $produit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produit $produit)
    {
        //
    }
}
