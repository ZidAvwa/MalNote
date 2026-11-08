<?php

namespace Tests\Feature;

use App\Models\ImportSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_sessions_with_progress(): void
    {
        $session = ImportSession::create([
            'filename' => 'Anime.txt',
            'mal_user_id' => '1',
            'mal_user_name' => 'Tester',
            'current_index' => 1,
        ]);

        $session->entries()->createMany([
            ['position' => 0, 'original_title' => 'A'],
            ['position' => 1, 'original_title' => 'B'],
        ]);

        $this->get(route('imports.index'))
            ->assertOk()
            ->assertSee('Anime.txt')
            ->assertSee('1 / 2')
            ->assertSee('In progress');
    }

    public function test_index_shows_empty_message(): void
    {
        $this->get(route('imports.index'))
            ->assertOk()
            ->assertSee('No imports yet.');
    }
}