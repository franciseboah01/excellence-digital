<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnseignantApprenantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $enseignant,
        public User $apprenant,
        public string $sujetMail,
        public string $contenu
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->sujetMail);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.enseignant-apprenant');
    }
}