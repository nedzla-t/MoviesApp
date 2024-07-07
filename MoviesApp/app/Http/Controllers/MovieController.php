<?php
namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MovieController extends Controller
{
    // Follow a movie
    public function follow(Movie $movie)
    {
        $user = auth()->user();
        $user->follows()->syncWithoutDetaching([$movie->id]);

        return response()->json(['message' => 'Movie followed']);
    }

    // Get followed movies
    public function getFollows()
    {
        $user = auth()->user();
        $follows = $user->follows()->paginate(10);

        return response()->json($follows);
    }

    public function favorite(Movie $movie)
    {
        $user = auth()->user();
        //dd($user);
        // Cache the favorite movie for the user
        $cacheKey = "user:{$user->id}:favorites";
        $favorites = Cache::get($cacheKey, []);

        if (!in_array($movie->id, $favorites)) {
            $favorites[] = $movie->id;
            Cache::put($cacheKey, $favorites, now()->addHours(2)); // Cache for 2 hours
        }

        return response()->json(['message' => 'Movie favorited']);
    }

    // Get favorite movies
    public function getFavorites()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $cacheKey = "user:{$user->id}:favorites";
        $favoriteIds = Cache::get($cacheKey, []);

        // Ensure favoriteIds is an array and contains valid IDs
        if (!is_array($favoriteIds) || empty($favoriteIds)) {
            return response()->json(['message' => 'No favorite movies found'], 404);
        }

        // Retrieve the favorite movies from the database
        $favorites = Movie::whereIn('id', $favoriteIds)->get();

        return response()->json($favorites);
    }

    public function index(Request $request)
    {
        $query = Movie::query();

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->get('title') . '%');
        }

        if ($request->has('description')) {
            $query->where('description', 'like', '%' . $request->get('description') . '%');
        }

        $movies = $query->paginate(10); // Pagination

        return response()->json($movies);
    }

    public function store(Request $request)
    {
        // Validate and create a new movie
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $movie = Movie::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'slug' => \Str::slug($validated['title']),
        ]);

        return response()->json($movie, 201);
    }

    public function show(Movie $movie)
    {
        return response()->json($movie);
    }

    public function update(Request $request, Movie $movie)
    {
        // Validate and update the movie
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
        ]);

        $movie->update($validated);
        return response()->json($movie);
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();
        return response()->json(null, 204);
    }
}
