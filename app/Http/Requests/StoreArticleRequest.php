<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'titre'     => 'required|string|max:200',
            'extrait'   => 'nullable|string|max:300',
            'contenu'   => 'required|string|min:10',
            'categorie' => 'required|string|max:50',
            'statut'    => 'required|in:publie,brouillon',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'   => 'Le titre est obligatoire.',
            'contenu.required' => 'Le contenu est obligatoire.',
            'contenu.min'      => 'Le contenu doit faire au moins 10 caractères.',
            'statut.in'        => 'Le statut doit être publié ou brouillon.',
        ];
    }
}