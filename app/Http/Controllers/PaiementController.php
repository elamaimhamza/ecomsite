<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Paiement;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaiementController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        $user = $request->get('auth_user');

        // Validation: Ensure we have a user (since your schema requires utilisateur_id)
        if (!$user) {
            return response()->json(['error' => 'User must be logged in'], 401);
        }

        $items = $request->input('items');
        $delivery = $request->input('delivery');
        $email = $request->input('email') ?? $user->email;

        // Start Transaction to ensure data integrity
        return DB::transaction(function () use ($request, $user, $items, $delivery, $email) {

            // 1. Initialize Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $lineItems = [];
            $totalAmount = 0;
            $dbOrderLines = [];

            // 2. Process Products (Fetch Price from DB)
            foreach ($items as $item) {
                $product = Produit::find($item['id']);

                if ($product) {
                    $quantity = $item['quantity'];
                    $price = $product->prix; // Assuming column is 'prix'
                    $totalAmount += $price * $quantity;

                    // Prepare data for LigneCommande
                    $dbOrderLines[] = [
                        'produit_id' => $product->id,
                        'quantite' => $quantity,
                        'prix_unitaire' => $price,
                        // timestamps are handled by Eloquent usually, or manually here
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Prepare Stripe Line Item
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => ['name' => $product->nom],
                            'unit_amount' => intval($price * 100),
                        ],
                        'quantity' => $quantity,
                    ];
                }
            }

            // 3. Process Delivery
            if ($delivery && isset($delivery['price'])) {
                $deliveryPrice = floatval($delivery['price']);
                $totalAmount += $deliveryPrice;

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => "Livraison: " . $delivery['title']],
                        'unit_amount' => intval($deliveryPrice * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            // 4. Create "Commande" Record
            $commande = Commande::create([
                'utilisateur_id' => $user->id,
                'montant_total' => $totalAmount,
                'statut' => 'En attente', // Matches your Enum
            ]);

            // 5. Create "LigneCommande" Records
            // We map the ID of the newly created command
            foreach ($dbOrderLines as &$line) {
                $line['commande_id'] = $commande->id;
            }
            LigneCommande::insert($dbOrderLines);

            // 6. Create Stripe Session
            $session = Session::create([
                'payment_method_types' => ['card', 'bancontact'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $email,
                'success_url' => env('CLIENT_URL') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('CLIENT_URL') . '/cart',
                'metadata' => [
                    'commande_id' => $commande->id, // Important link for Webhook
                ],
            ]);

            // 7. Update Commande with Stripe Session ID
            $commande->update(['stripe_session_id' => $session->id]);

            return response()->json(['url' => $session->url]);
        });
    }
}
