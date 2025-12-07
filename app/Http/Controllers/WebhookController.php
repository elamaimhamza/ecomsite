<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Strategy 1: Find by Metadata (Preferred)
            $commandeId = $session->metadata->commande_id ?? null;

            // Strategy 2: Find by Session ID (Backup)
            if (!$commandeId) {
                $commande = Commande::where('stripe_session_id', $session->id)->first();
            } else {
                $commande = Commande::find($commandeId);
            }

            if ($commande) {
                // Update status to match your Enum
                $commande->update(['statut' => 'Payée']);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
