<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePodcastRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Animateur ou Admin peuvent créer
        return in_array(auth()->user()->role, ['animateur', 'administrateur']);
    }

    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|min:10',
            'categorie' => 'required|string|max:100', 
            'image' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre du podcast est obligatoire.',
            'description.min' => 'La description doit dépasser 10 caractères.',
            'categorie.required' => 'La catégorie est obligatoire.',
            'image.mimes' => 'Le fichier doit être au format png, jpg ou jpeg.',
            'image.max' => 'L\'image ne doit pas dépasser 2MB.',
        ];
    }
}