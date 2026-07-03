<?php

namespace App\Mail;

use App\Models\Food;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewFoodAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $food;

    /**
     * Create a new message instance.
     */
    public function __construct(Food $food)
    {
        $this->food = $food;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '😋 New Surplus Food Deal Listed: ' . $this->food->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-food-alert',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
