<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'montant_total' => 'required|numeric|min:1',
            'montant_paye'  => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:especes,mobile_money,virement,autre',
            'formation_id'  => 'nullable|exists:formations,id',
            'service_id'    => 'nullable|exists:services,id',
            'demande_id'    => 'nullable|exists:demandes_service,id',
            'notes'         => 'nullable|string|max:500',
            'date_paiement' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => 'Le client est obligatoire.',
            'montant_total.required' => 'Le montant total est obligatoire.',
            'montant_total.min'      => 'Le montant total doit être positif.',
            'montant_paye.min'       => 'Le montant payé ne peut pas être négatif.',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // S'assurer que montant_paye ne dépasse pas montant_total
        if ($this->montant_paye && $this->montant_total) {
            $this->merge([
                'montant_paye' => min($this->montant_paye, $this->montant_total),
            ]);
        }
    }
}