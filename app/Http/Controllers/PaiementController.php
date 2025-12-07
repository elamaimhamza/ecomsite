<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Produit;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaiementController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        // 1. Initialize Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $items = $request->input('items'); // [{id: 1, quantity: 2}, ...]
        $delivery = $request->input('delivery'); // {id: 'express', price: 12.95, ...}

        $lineItems = [];

        // 2. Loop through cart items and fetch REAL details from DB
        foreach ($items as $item) {
            $product = Produit::find($item['id']);

            if ($product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->nom,
                            // 'images' => [$product->image_url], // Optional
                        ],
                        'unit_amount' => intval($product->prix * 100), // Convert to cents
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
        }

        // 3. Add Delivery Cost
        if ($delivery && isset($delivery['price']) && $delivery['price'] > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => "Livraison: " . $delivery['title'],
                    ],
                    'unit_amount' => intval($delivery['price'] * 100), // Convert to cents
                ],
                'quantity' => 1,
            ];
        }

        // 4. Create the Session
        try {
            $session = Session::create([
                'payment_method_types' => ['card', 'bancontact'], // Bancontact for Belgium
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => env('CLIENT_URL') . '/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => env('CLIENT_URL') . '/panier',
                'metadata' => [
                    'delivery_method' => $delivery['id'] ?? 'bpost_home',
                    // You can add user_id here if logged in
                ],
            ]);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
