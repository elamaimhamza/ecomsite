<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        // Get the start of the current calendar month
        $startOfMonth = now()->startOfMonth();

        // Define the statuses that count as "Money Earned"
        $paidStatuses = ['Payée', 'Expédiée', 'Livrée'];

        // --- LOGIC FOR BEST SELLER ---
        $bestSeller = LigneCommande::select('produit_id', DB::raw('SUM(quantite) as total_sold'))
            ->groupBy('produit_id')
            ->orderByDesc('total_sold')
            ->with('produit') // Load the product details to get the name
            ->first();

        return response()->json([
            // 1. Total users in the table
            'active_users' => Utilisateur::count(),

            // 2. Users created since the 1st of this month
            'new_this_month' => Utilisateur::where('created_at', '>=', $startOfMonth)->count(),

            // 3. Admin count 
            // Note: Update 'is_admin' to match your column name (e.g., 'role' => 'admin')
            'admins' => Utilisateur::where('type_utilisateur', 'Gestionnaire')->count(),

            // 4. Total orders count
            'total_commands' => Commande::count(),

            // 5. Sum of 'montant_total' ONLY where status is 'Payée'
            'total_earned' => Commande::whereIn('statut', $paidStatuses)->sum('montant_total'),

            // Average spend per paid order
            'average_basket' => Commande::where('statut', 'Payée')->avg('montant_total'),

            // Returns: [{"statut": "Payée", "count": 10}, {"statut": "Livrée", "count": 5}]
            'orders_by_status' => Commande::selectRaw('statut, count(*) as count')
                ->groupBy('statut')
                ->get(),

            // --- NEW: Daily Revenue for the last 7 days ---
            'daily_revenue' => collect(range(0, 6))->map(function ($i) use ($paidStatuses) {
                $date = now()->subDays($i);
                return [
                    'date' => $date->format('d/m'),
                    'total' => Commande::whereDate('created_at', $date)
                        ->whereIn('statut', $paidStatuses) // <--- Updated here too
                        ->sum('montant_total')
                ];
            })->reverse()->values(),

            // 2. Orders breakdown (for Pie Chart)
            'orders_distribution' => Commande::selectRaw('statut, count(*) as count')
                ->groupBy('statut')
                ->get(),
            // --- NEW: TOP PRODUCT STAT ---
            'top_product' => $bestSeller ? [
                // Assuming your Product model has a column 'nom' or 'titre'
                'name' => $bestSeller->produit->nom ?? 'Produit inconnu',
                'count' => $bestSeller->total_sold
            ] : null,
        ]);
    }
}
