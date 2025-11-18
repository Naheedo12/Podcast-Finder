<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Podcast;

/**
 * @OA\Schema(
 *     schema="Episode",
 *     type="object",
 *     title="Episode",
 *     description="Modèle d'épisode de podcast",
 *     required={"titre", "description", "audio", "podcast_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="titre", type="string", example="Episode 1 : Introduction"),
 *     @OA\Property(property="description", type="string", example="Premier épisode de notre podcast"),
 *     @OA\Property(property="audio", type="string", example="https://cloudinary.com/audio.mp3"),
 *     @OA\Property(property="podcast_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
 *     @OA\Property(
 *         property="podcast",
 *         ref="#/components/schemas/Podcast",
 *         description="Podcast auquel appartient l'épisode"
 *     )
 * )
 */
class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'audio',
        'podcast_id'
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }
}