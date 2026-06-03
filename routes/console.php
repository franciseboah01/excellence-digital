<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

// Test d'envoi d'email simple
Artisan::command('send-mail', function () {

    Mail::to('franciseboah87@gmail.com')
        ->send(new ContactMail([
            'nom'     => 'Test Utilisateur',
            'email'   => 'test@test.com',
            'sujet'   => 'Test depuis Excellence Digital Center',
            'message' => 'Ceci est un email de test envoyé depuis Laravel via Mailtrap.',
        ]));

    $this->info('✅ Email envoyé ! Vérifiez votre boîte Mailtrap.');

})->purpose('Tester l\'envoi d\'email');