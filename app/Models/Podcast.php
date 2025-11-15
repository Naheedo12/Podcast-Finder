<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Episode;

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