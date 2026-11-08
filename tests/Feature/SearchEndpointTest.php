<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchEndpointTest extends TestCase
{
    public function test_search_returns_mapped_results(): void
    {
        Cache::flush();

        Http::fake([
            'graphql.anilist.co' => Http::response([
                'data' => ['Page' => ['media' => [
                    [
                        'idMal' => 20,
                        'title' => ['romaji' => 'Naruto', 'english' => 'Naruto'],
                        'format' => 'TV',
                        'episodes' => 220,
                        'averageScore' => 80,
                        'seasonYear' => 2002,
                        'coverImage' => ['large' => 'https://example.com/n.jpg'],
                    ],
                    [
                        'idMal' => null, // should be skipped
                        'title' => ['romaji' => 'No MAL id'],
                    ],
                ]]],
            ]),
        ]);

        $this->getJson('/search?q=Naruto')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.mal_id', 20)
            ->assertJsonPath('0.score', '8');
    }

    public function test_search_requires_a_query(): void
    {
        $this->getJson('/search')->assertStatus(422);
    }
}