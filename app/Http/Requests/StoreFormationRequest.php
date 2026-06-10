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
            'module_id'   => 'required|exists:modules,id',
            'duree'       => 'nullable|string|max:50',
            'prix'        => 'nullable|integer|min:0',
            'statut'      => 'required|in:publie,brouillon',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required'       => 'Le titre de la formation est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'module_id.required'   => 'Le module est obligatoire.',
            'module_id.exists'     => 'Le module sélectionné n\'existe pas.',
            'prix.integer'         => 'Le prix doit être un nombre entier.',
            'image.image'          => 'Le fichier doit être une image.',
            'image.max'            => 'L\'image ne doit pas dépasser 2 MB.',
        ];
    }
}