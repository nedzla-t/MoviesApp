<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MovieControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * 
    * @test
    */
    public function testFollowMovie()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->postJson('/api/auth/movies/' . $movie->id . '/follow');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Movie followed',
                 ]);

        $this->assertDatabaseHas('follows', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ]);
    }
    
    /**
    * 
    * @test
    */
    public function testFavoriteMovie()
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $response = $this->actingAs($user, 'api')
                         ->postJson('/api/auth/movies/' . $movie->id . '/favorite');

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Movie favorited',
                 ]);

        $this->assertTrue(
            Cache::has("user:{$user->id}:favorites"),
            'Cache key should exist after favoriting a movie'
        );

        $favorites = Cache::get("user:{$user->id}:favorites");
        $this->assertContains($movie->id, $favorites);
    }

    /**
    * 
    * @test
    */
    public function testGetFavoriteMovies()
    {
        $user = User::factory()->create();
        $movie1 = Movie::factory()->create();
        $movie2 = Movie::factory()->create();
    
        // Simulate favoriting movies for the user
        $user->favorites()->sync([$movie1->id, $movie2->id]);
    
        // Authenticate as the user and fetch favorite movies
        $response = $this->actingAs($user, 'api')
                         ->getJson('/api/auth/movies-favorites');
    
        $response->assertOK()
                 ->assertJsonFragment(['id' => $movie1->id])
                 ->assertJsonFragment(['id' => $movie2->id]);
    }

        /**
    * 
    * @test
    */
    public function testFetchMoviesWithSearch()
    {
        Movie::factory()->create(['title' => 'Example Movie 1', 'description' => 'Lorem ipsum dolor sit amet']);
        Movie::factory()->create(['title' => 'Example Movie 2', 'description' => 'Lorem ipsum dolor sit amet']);
    
        Movie::factory()->count(8)->create();

        $response = $this->getJson('/api/auth/movies?title=Example&description=Lorem');

        $response->assertStatus(200)
             ->assertJsonCount(2, 'data')
             ->assertJsonStructure([
                 'data' => [
                     '*' => ['id', 'title', 'description', 'created_at', 'updated_at'],
                 ],
        ]);
    }


}
