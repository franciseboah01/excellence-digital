<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $admin,
        public User $destinataire,
        public string $sujetMail,
        public string $contenu
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->sujetMail);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.admin-client');
    }
}