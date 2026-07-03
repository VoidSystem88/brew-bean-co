<?php

namespace App\Mail;

use App\Models\RestockOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RestockOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $branch;
    public $supplier;
    public $items;

    public function __construct(RestockOrder $order)
    {
        $this->order = $order;
        $this->branch = $order->branch;
        $this->supplier = $order->supplier;
        $this->items = $order->items;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '?? Restock Order #' . $this->order->order_number . ' - ' . $this->branch->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.restock-order',
        );
    }
}
