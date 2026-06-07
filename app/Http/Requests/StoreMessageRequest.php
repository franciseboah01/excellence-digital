<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'destinataire_id' => [
                'required',
                'exists:users,id',
                'different:' . auth()->id(),
            ],
            'contenu' => 'required|string|min:1|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'destinataire_id.required'  => 'Le destinataire est obligatoire.',
            'destinataire_id.different' => 'Vous ne pouvez pas vous envoyer un message.',
            'contenu.required'          => 'Le message ne peut pas être vide.',
            'contenu.max'               => 'Le message ne doit pas dépasser 1000 caractères.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'contenu' => e(trim($this->contenu)),
        ]);
    }
}