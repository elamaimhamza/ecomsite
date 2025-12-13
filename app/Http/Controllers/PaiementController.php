<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use App\Models\Livraison;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Transporteur;
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
        $deliveryInput = $request->input('delivery');

        // USE USER DATA DIRECTLY
        $userEmail = $user->email;
        $userAddress = $user->adresse;

        // Start Transaction to ensure data integrity
        return DB::transaction(function () use ($user, $items, $deliveryInput, $userEmail, $userAddress) {

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $lineItems = [];
            $totalAmount = 0;
            $dbOrderLines = [];

            // 2. Process Products
            foreach ($items as $item) {
                $product = Produit::find($item['id']);

                if ($product) {
                    $quantity = $item['quantity'];
                    $price = $product->prix;
                    $totalAmount += $price * $quantity;

                    $dbOrderLines[] = [
                        'produit_id' => $product->id,
                        'quantite' => $quantity,
                        'prix_unitaire' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

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

            // 3. Process Delivery (Secure DB Lookup)
            $transporteur = null;

            if ($deliveryInput && isset($deliveryInput['id'])) {
                $transporteur = Transporteur::where('id', $deliveryInput['id'])->first();

                if ($transporteur) {
                    $deliveryPrice = $transporteur->prix;
                    $totalAmount += $deliveryPrice;

                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => ['name' => "Livraison: " . $transporteur->nom],
                            'unit_amount' => intval($deliveryPrice * 100),
                        ],
                        'quantity' => 1,
                    ];
                }
            }

            // 4. Create Commande
            $commande = Commande::create([
                'utilisateur_id' => $user->id,
                'montant_total' => $totalAmount,
                'statut' => 'Payée',
            ]);

            // 5. Insert Order Lines
            foreach ($dbOrderLines as &$line) {
                $line['commande_id'] = $commande->id;
            }
            LigneCommande::insert($dbOrderLines);

            // 6. Create Delivery Record (Using User's Address)
            if ($transporteur) {
                Livraison::create([
                    'commande_id' => $commande->id,
                    'transporteur_id' => $transporteur->id,
                    'mode_livraison' => $transporteur->nom,
                    'adresse_livraison' => $userAddress, // Saved from User Profile
                    'numero_suivi' => null,
                ]);
            }

            // 7. Create Stripe Session
            $session = Session::create([
                'payment_method_types' => ['card', 'bancontact'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $userEmail, // Pre-fills Stripe email field
                'success_url' => env('CLIENT_URL') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('CLIENT_URL') . '/cart',
                'metadata' => [
                    'commande_id' => $commande->id,
                ],
            ]);

            $commande->update(['stripe_session_id' => $session->id]);

            return response()->json(['url' => $session->url]);
        });
    }
}
