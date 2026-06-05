<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class BienvenueMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $verificationUrl;

    public function __construct(public User $user)
    {
        // Générer le lien de vérification email
        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Bienvenue sur Excellence Digital Center !',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.bienvenue');
    }
}