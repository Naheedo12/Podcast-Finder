<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePodcastRequest extends FormRequest
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
            'categorie' => 'sometimes|string|max:100',
            'image' => 'sometimes|file|mimes:png,jpg,jpeg',
        ];
    }

    public function messages(): array
    {
        return [
            'titre.string' => 'Le titre doit être une chaîne de caractères.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'description.min' => 'La description doit dépasser 10 caractères.',
            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
            'categorie.max' => 'La catégorie ne peut pas dépasser 100 caractères.',
            'image.mimes' => 'L\'image doit être au format PNG, JPG ou JPEG.',
        ];
    }
}