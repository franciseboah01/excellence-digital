<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $expediteur,
        public User $destinataire,
        public string $contenu
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "💬 Nouveau message de {$this->expediteur->prenom} — Excellence Digital Center",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.nouveau-message');
    }
}