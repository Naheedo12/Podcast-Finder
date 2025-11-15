<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodeRequest extends FormRequest
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
            'titre' => 'sometimes|string|max:255',
            'description' => 'nullable|string|min:10',
            'audio' => 'sometimes|file|mimes:mp3,wav'
        ];
    }

    public function messages(): array
    {
        return [
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.min' => 'La description doit dépasser 10 caractères.',
            'audio.mimes' => 'Le fichier audio doit être au format MP3 ou WAV.',
        ];
    }
}