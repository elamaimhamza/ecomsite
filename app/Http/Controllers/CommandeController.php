<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            'statut' => 'required|in:Payée,Expédiée,Livrée,Annulée'
        ]);

        // 2. Find the order AND the user (needed for the email)
        $commande = Commande::with('utilisateur')->findOrFail($id);

        // Check if the status is actually different before updating
        // This prevents sending an email if the admin clicks "Update" without changing anything
        if ($commande->statut !== $validated['statut']) {

            // Update the status
            $commande->update([
                'statut' => $validated['statut']
            ]);

            // 3. Send Email Notification
            try {
                // Check if user exists (just in case the user was deleted but order remains)
                if ($commande->utilisateur) {
                    Mail::to($commande->utilisateur->email)
                        ->send(new OrderStatusUpdated($commande));
                }
            } catch (\Exception $e) {
                // Log the error so you can debug it, but don't break the API response
                Log::error("Failed to send order update email for Order ID {$id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'commande' => $commande
        ]);
    }

    /**
     * Display a listing of the resource for the logged-in user.
     */
    public function getUserOrders(Request $request)
    {
        // 1. Get the authenticated user's ID
        $user = $request->get('auth_user');
        $userId = $user->id;
        // 2. Query commands specifically for this user
        // Assuming your foreign key is 'utilisateur_id' based on your relation name
        $commandes = Commande::where('utilisateur_id', $userId)
            ->with('ligneCommandes')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'commandes' => $commandes
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function getOneOrder(Request $request, $id)
    {
        $userId = $request->get('auth_user')->id;

        // 1. Find the order where ID matches AND user matches
        $commande = Commande::where('id', $id)
            ->where('utilisateur_id', $userId) // Security check
            ->with(['ligneCommandes.produit', 'livraison']) // Eager load items and their products
            ->first();

        // 2. If not found or not owned by user, return error
        if (!$commande) {
            return response()->json(['message' => 'Commande introuvable ou accès refusé'], 404);
        }

        return response()->json([
            'commande' => $commande
        ]);
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
     * Remove the specified resource from storage.
     */
    public function destroy(Commande $commande)
    {
        //
    }
}
