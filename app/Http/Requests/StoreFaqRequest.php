<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'question'  => 'required|string|max:300',
            'reponse'   => 'required|string|min:5',
            'categorie' => 'required|string|max:50',
            'ordre'     => 'nullable|integer|min:0|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'question.required'  => 'La question est obligatoire.',
            'reponse.required'   => 'La réponse est obligatoire.',
            'categorie.required' => 'La catégorie est obligatoire.',
        ];
    }
}