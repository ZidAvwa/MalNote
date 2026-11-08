<?php

namespace App\Services;

class BatchParser
{
    /**
     * Turn the raw text of an anime list into a flat array of search titles.
     * "Naruto S1.S2" => ["Naruto", "Naruto Season 2"]
     * "Naruto M1"    => ["Naruto Movie 1"]
     */
    public function parse(string $content): array
    {
        $titles = [];

        foreach (preg_split('/\R/', $content) as $line) {
            $line = trim($line);

            // skip blank lines, [headers] and #comments
            if ($line === '' || $line[0] === '[' || $line[0] === '#') {
                continue;
            }

            if (str_contains($line, 'LiveAction') && ! str_contains($line, 'S1')) {
                $line = trim(preg_replace('/\s+/', ' ', str_replace('LiveAction', '', $line)));
            }

            $parts = explode(' ', $line);
            $last = array_pop($parts);
            $base = implode(' ', $parts);

            // last word looks like S2, M1, S1.S2.M1 ...
            if ($base !== '' && preg_match('/^[SM]\d+(\.[SM]\d+)*$/', $last)) {
                foreach (explode('.', $last) as $token) {
                    $number = substr($token, 1);

                    if ($token[0] === 'S') {
                        $titles[] = trim($base . ($number !== '1' ? " Season $number" : ''));
                    } else {
                        $titles[] = trim("$base Movie $number");
                    }
                }
            } else {
                $titles[] = $line;
            }
        }

        return $titles;
    }
}
