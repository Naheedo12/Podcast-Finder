<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Podcast;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_create_podcast()
    {
        $user = User::factory()->create(['role' => 'utilisateur']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/podcasts', [
            'titre' => 'Test Podcast',
            'description' => 'Description test',
            'categorie' => 'Technology'
        ]);

        $response->assertStatus(403);
    }

    public function test_podcast_requires_valid_data()
    {
        $admin = User::factory()->create(['role' => 'administrateur']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/podcasts', [
            'titre' => '', // Titre vide
            'description' => 'Court', // Description trop courte
            'categorie' => '' // Catégorie vide
        ]);

        $response->assertStatus(422); // Validation error
    }
}