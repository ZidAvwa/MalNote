<?php

namespace Tests\Unit;

use App\Services\BatchParser;
use PHPUnit\Framework\TestCase;

class BatchParserTest extends TestCase
{
    public function test_plain_title_is_kept(): void
    {
        $this->assertSame(['Attack on Titan'], (new BatchParser)->parse('Attack on Titan'));
    }

    public function test_seasons_are_expanded(): void
    {
        $this->assertSame(
            ['Naruto', 'Naruto Season 2', 'Naruto Season 3'],
            (new BatchParser)->parse('Naruto S1.S2.S3')
        );
    }

    public function test_movies_are_expanded(): void
    {
        $this->assertSame(['Naruto Movie 1'], (new BatchParser)->parse('Naruto M1'));
    }

    public function test_headers_comments_and_blank_lines_are_skipped(): void
    {
        $content = "[Header]\n# comment\n\nBleach\n";
        $this->assertSame(['Bleach'], (new BatchParser)->parse($content));
    }

    public function test_live_action_rule(): void
    {
        $parser = new BatchParser;

        $this->assertSame(['Kamen Rider Season 2'], $parser->parse('Kamen Rider LiveAction S2'));
        $this->assertSame(['One Piece LiveAction'], $parser->parse('One Piece LiveAction S1'));
    }
}