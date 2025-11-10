<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Podcast;


class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'audio',
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }
}
