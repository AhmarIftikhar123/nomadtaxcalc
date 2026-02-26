<?php

namespace App\Mail;

use App\Models\UserCalculation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaxResultsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UserCalculation $calculation,
        public string $shareUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Tax Calculation Results — ' . $this->calculation->tax_year,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.tax-results',
        );
    }
}
