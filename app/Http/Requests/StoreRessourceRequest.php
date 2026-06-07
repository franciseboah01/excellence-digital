<?php

namespace App\Http\Requests;

use App\Services\FichierService;
use Illuminate\Foundation\Http\FormRequest;

class StoreRessourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasRole(['admin', 'enseignant']);
    }

    public function rules(): array
    {
        $typesAutorises = implode(',', FichierService::typesAutorises());
        $tailleMax      = FichierService::tailleMaxMb() * 1024; // KB

        return [
            'formation_id' => 'required|exists:formations,id',
            'niveau_id'    => 'nullable|exists:niveaux_formation,id',
            'type'         => 'required|in:pdf,ebook,lien,video,document',
            'titre'        => 'required|string|max:200',
            'description'  => 'nullable|string|max:500',
            'fichier'      => [
                'required_if:type,pdf,ebook,document',
                'nullable',
                'file',
                "mimes:{$typesAutorises}",
                "max:{$tailleMax}",
            ],
            'lien_url' => 'required_if:type,lien,video|nullable|url|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'formation_id.required'      => 'La formation est obligatoire.',
            'type.required'              => 'Le type de ressource est obligatoire.',
            'titre.required'             => 'Le titre est obligatoire.',
            'fichier.required_if'        => 'Un fichier est obligatoire pour ce type de ressource.',
            'fichier.mimes'              => 'Type de fichier non autorisé.',
            'fichier.max'                => 'Le fichier dépasse la taille maximale autorisée.',
            'lien_url.required_if'       => 'L\'URL est obligatoire pour ce type de ressource.',
            'lien_url.url'               => 'L\'URL n\'est pas valide.',
        ];
    }
}