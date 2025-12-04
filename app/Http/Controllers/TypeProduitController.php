<?php

namespace App\Http\Controllers;

use App\Models\TypeProduit;
use Illuminate\Http\Request;

class TypeProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Simulation de la récupération des données via le modèle:
        $productTypes = TypeProduit::all()->toArray();

        // Retourne la liste au format JSON
        return response()->json($productTypes);
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
    public function show(TypeProduit $typeProduit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeProduit $typeProduit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeProduit $typeProduit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeProduit $typeProduit)
    {
        //
    }
}
