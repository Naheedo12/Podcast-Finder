<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Podcast;
use App\Models\Episode;
use Laravel\Sanctum\Sanctum;

class EpisodeTest extends TestCase
{

    public function test_user_cannot_create_episode()
    {
        $user = User::factory()->create(['role' => 'utilisateur']);
        $podcast = Podcast::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/podcasts/{$podcast->id}/episodes", [
            'titre' => 'Test Episode',
            'description' => 'Description test',
            'podcast_id' => $podcast->id,
            'audio' => 'https://example.com/audio.mp3'
        ]);

        $response->assertStatus(403);
    }
}