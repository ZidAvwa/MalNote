<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class AniListService
{
    private const URL = 'https://graphql.anilist.co';

    private const QUERY = <<<'GRAPHQL'
    query ($search: String) {
      Page (page: 1, perPage: 7) {
        media (search: $search, type: ANIME) {
          idMal
          title { romaji english }
          format
          episodes
          averageScore
          seasonYear
          coverImage { large }
        }
      }
    }
    GRAPHQL;

    /**
     * Returns an array of results, or null if the API failed.
     */
    public function search(string $query): ?array
    {
        try {
            $response = Http::timeout(10)
                ->retry(3, 2000, function ($exception) {
                    $status = $exception->response?->status();
                    return $status === null || in_array($status, [429, 500, 502, 503, 504]);
                }, throw: false)
                ->post(self::URL, [
                    'query' => self::QUERY,
                    'variables' => ['search' => $query],
                ]);
        } catch (ConnectionException $e) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $results = [];

        foreach ($response->json('data.Page.media', []) as $item) {
            if (empty($item['idMal'])) {
                continue; // same as your `if not item.get("idMal"): continue`
            }

            $score = isset($item['averageScore'])
                ? (string) ($item['averageScore'] / 10)
                : 'N/A';

            $results[] = [
                'mal_id'    => $item['idMal'],
                'title'     => $item['title']['romaji'] ?? $item['title']['english'] ?? 'Unknown',
                'type'      => $item['format'] ?? 'TV',
                'year'      => $item['seasonYear'] ?? 'N/A',
                'episodes'  => $item['episodes'] ?? 0,
                'score'     => $score,
                'image_url' => $item['coverImage']['large'] ?? '',
            ];
        }

        return $results;
    }
}