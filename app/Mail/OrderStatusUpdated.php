<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;

    /**
     * Create a new message instance.
     */
    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mise à jour de votre commande #' . str_pad($this->commande->id, 4, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return $this->subject('Mise à jour de votre commande #' . str_pad($this->commande->id, 4, '0', STR_PAD_LEFT))
    //         ->view('emails.orders.status_updated');
    // }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Mise à jour de votre commande #' . str_pad($this->commande->id, 4, '0', STR_PAD_LEFT))
            ->view('emails.orders.status_updated');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
