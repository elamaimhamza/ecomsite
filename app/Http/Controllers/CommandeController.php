<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // We use 'with' to Eager Load the user data (prevents N+1 query problem)
        // We order by newest first
        $commandes = Commande::with('utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'commandes' => $commandes
        ]);
    }

    /**
     * Update the status of a specific order
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Validate that the status is one of your Enum values
        $validated = $request->validate([
            'statut' => 'required|in:En attente,Payée,Expédiée,Livrée,Annulée'
        ]);

        // 2. Find and Update
        $commande = Commande::findOrFail($id);

        $commande->update([
            'statut' => $validated['statut']
        ]);

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'commande' => $commande
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
     * Get details of a single order
     */
    public function show($id)
    {
        $commande = Commande::with([
            'utilisateur',
            // Load line items AND the associated product details (for images)
            'ligneCommandes.produit'
        ])->findOrFail($id);

        return response()->json($commande);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commande $commande)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commande $commande)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commande $commande)
    {
        //
    }
}
