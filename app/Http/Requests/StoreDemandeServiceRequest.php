<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDemandeServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Visiteurs et clients autorisés
    }

    public function rules(): array
    {
        return [
            'nom_visiteur'       => 'required|string|max:100',
            'email_visiteur'     => 'required|email|max:150',
            'telephone_visiteur' => 'nullable|string|max:20',
            'service_id'         => 'required|exists:services,id',
            'message'            => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_visiteur.required'   => 'Votre nom est obligatoire.',
            'email_visiteur.required' => 'Votre email est obligatoire.',
            'email_visiteur.email'    => 'L\'email n\'est pas valide.',
            'service_id.required'     => 'Veuillez choisir un service.',
            'service_id.exists'       => 'Le service sélectionné n\'existe pas.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Limiter la recherche à 50 caractères
        if ($this->has('message')) {
            $this->merge([
                'message' => substr(trim($this->message), 0, 1000),
            ]);
        }
    }
}