<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $items;
    public $branch;

    public function __construct($items, $branch)
    {
        $this->items = $items;
        $this->branch = $branch;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '?? Low Stock Alert - ' . $this->branch->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock',
        );
    }
}
