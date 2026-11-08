<?php

namespace Tests\Feature;

use App\Models\ImportSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_only_includes_submitted_entries(): void
    {
        $session = ImportSession::create([
            'filename' => 'Anime.txt',
            'mal_user_id' => '123',
            'mal_user_name' => 'TestUser',
        ]);

        $session->entries()->createMany([
            ['position' => 0, 'original_title' => 'Naruto', 'mal_id' => 20,
             'final_title' => 'Naruto', 'score' => 8, 'status' => 'Completed'],
            ['position' => 1, 'original_title' => 'Skipped One', 'skipped' => true],
        ]);

        $response = $this->get(route('imports.export', $session));

        $response->assertOk();
        $xml = $response->getContent();

        $this->assertStringContainsString('<user_id>123</user_id>', $xml);
        $this->assertStringContainsString('<user_name>TestUser</user_name>', $xml);
        $this->assertStringContainsString('<series_animedb_id>20</series_animedb_id>', $xml);
        $this->assertStringContainsString('<my_score>8</my_score>', $xml);
        $this->assertStringNotContainsString('Skipped One', $xml);
    }
}