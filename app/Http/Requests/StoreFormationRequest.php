<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'titre'       => 'required|string|max:200',
            'description' => 'required|string|min:10',
            'niveau'      => 'required|in:debutant,intermediaire,avance',
            'duree'       => 'nullable|string|max:50',
            'statut'      => 'required|in:publie,brouillon',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'       => 'Le titre de la formation est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'niveau.required'      => 'Le niveau est obligatoire.',
            'niveau.in'            => 'Le niveau doit être : débutant, intermédiaire ou avancé.',
            'image.image'          => 'Le fichier doit être une image.',
            'image.max'            => 'L\'image ne doit pas dépasser 2 MB.',
        ];
    }
}