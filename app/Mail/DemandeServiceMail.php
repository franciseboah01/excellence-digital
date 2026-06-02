<?php

namespace App\Mail;

use App\Models\DemandeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DemandeServiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DemandeService $demande) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Confirmation de votre demande — Excellence Digital Center',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.demande-service',
        );
    }
}