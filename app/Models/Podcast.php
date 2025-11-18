<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Episode;

/**
 * @OA\Schema(
 *     schema="Podcast",
 *     type="object",
 *     title="Podcast",
 *     description="Modèle de podcast",
 *     required={"titre", "categorie", "description"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="titre", type="string", example="Mon Podcast Tech"),
 *     @OA\Property(property="categorie", type="string", example="Technologie"),
 *     @OA\Property(property="description", type="string", example="Un podcast sur les nouvelles technologies"),
 *     @OA\Property(property="image", type="string", example="https://cloudinary.com/image.jpg"),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User",
 *         description="Animateur du podcast"
 *     ),
 *     @OA\Property(
 *         property="episodes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Episode"),
 *         description="Liste des épisodes du podcast"
 *     )
 * )
 */
class Podcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'categorie',
        'description',
        'image',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}