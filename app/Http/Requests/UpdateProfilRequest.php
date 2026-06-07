<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'nom'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20|regex:/^[+\d\s\-\(\)]{6,20}$/',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required'        => 'Le nom est obligatoire.',
            'prenom.required'     => 'Le prénom est obligatoire.',
            'telephone.regex'     => 'Le numéro de téléphone n\'est pas valide.',
            'avatar.image'        => 'L\'avatar doit être une image.',
            'avatar.max'          => 'L\'avatar ne doit pas dépasser 2 MB.',
        ];
    }
}