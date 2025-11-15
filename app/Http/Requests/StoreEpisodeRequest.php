<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['administrateur', 'animateur']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string|min:10',
            'audio' => 'required|file|mimes:mp3,wav',
            'podcast_id' => 'required|exists:podcasts,id'
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre de l\'épisode est obligatoire.',
            'description.min' => 'La description doit dépasser 10 caractères.',
            'audio.required' => 'Le fichier audio est obligatoire.',
            'audio.mimes' => 'Le fichier audio doit être au format MP3 ou WAV.',
            'podcast_id.required' => 'L\'ID du podcast est obligatoire.',
            'podcast_id.exists' => 'Le podcast spécifié n\'existe pas.',
        ];
    }
}