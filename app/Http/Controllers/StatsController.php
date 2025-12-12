<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function index()
    {
        // Get the start of the current calendar month
        $startOfMonth = now()->startOfMonth();

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
            'total_earned' => Commande::where('statut', 'Payée')->sum('montant_total'),

            // Average spend per paid order
            'average_basket' => Commande::where('statut', 'Payée')->avg('montant_total'),

            // Returns something like: [{"statut": "Payée", "count": 10}, {"statut": "Annulée", "count": 2}]
            'orders_by_status' => Commande::selectRaw('statut, count(*) as count')
                ->groupBy('statut')
                ->pluck('count', 'statut'),

            // This requires a tiny loop, but it's very readable
            'daily_revenue' => collect(range(0, 6))->map(function ($daysAgo) {
                $date = now()->subDays($daysAgo)->format('Y-m-d');
                return [
                    'date' => $date,
                    'total' => Commande::whereDate('created_at', $date)
                        ->where('statut', 'Payée')
                        ->sum('montant_total')
                ];
            })->reverse()->values(),

            // 2. Orders breakdown (for Pie Chart)
            'orders_distribution' => Commande::selectRaw('statut, count(*) as count')
                ->groupBy('statut')
                ->get(),
        ]);
    }
}
