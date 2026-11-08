<?php

namespace App\Http\Controllers;

use App\Services\AniListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function __invoke(Request $request, AniListService $anilist): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'max:100'],
        ]);

        $query = trim($validated['q']);

        // cache for 10 minutes so "Go Back" and repeated searches don't hit AniList again
        $results = Cache::remember(
            'anilist:' . mb_strtolower($query),
            now()->addMinutes(10),
            fn () => $anilist->search($query)
        );

        if ($results === null) {
            return response()->json(['message' => 'AniList is unavailable, try again.'], 502);
        }

        return response()->json($results);
    }
}