<?php

namespace App\Mail;

use App\Models\UserWishProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * AdminNewPriceAlertMail
 *
 * Notifies the admin when a user creates a new price alert (target_price set).
 */
class AdminNewPriceAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly UserWishProduct $wish
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Novo alerta de preço: ' . ($this->wish->product->name ?? 'Produto #' . $this->wish->product_id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-new-price-alert',
        );
    }
}
