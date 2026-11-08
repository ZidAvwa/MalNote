<?php

namespace App\Services;

use App\Models\ImportSession;

class MalXmlBuilder
{
    // AniList format => MAL type label
    private const TYPE_MAP = [
        'TV'       => 'TV',
        'TV_SHORT' => 'TV',
        'MOVIE'    => 'Movie',
        'SPECIAL'  => 'Special',
        'OVA'      => 'OVA',
        'ONA'      => 'ONA',
        'MUSIC'    => 'Music',
    ];

    public function build(ImportSession $session): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8" ?>',
            '<myanimelist>',
            '<myinfo>',
            '<user_id>' . $this->escape($session->mal_user_id) . '</user_id>',
            '<user_name>' . $this->escape($session->mal_user_name) . '</user_name>',
            '<user_export_type>1</user_export_type>',
            '</myinfo>',
        ];

        $entries = $session->entries()
            ->where('skipped', false)
            ->whereNotNull('mal_id')
            ->get(); // already ordered by position

        foreach ($entries as $entry) {
            // "]]>" would end the CDATA block early, so split it
            $title = str_replace(']]>', ']]]]><![CDATA[>', $entry->final_title);
            $type = self::TYPE_MAP[$entry->series_type] ?? 'TV';
            $episodes = (int) $entry->series_episodes;
            $watched = $entry->status === 'Completed' ? $episodes : 0;

            array_push(
                $lines,
                '<anime>',
                "<series_animedb_id>{$entry->mal_id}</series_animedb_id>",
                "<series_title><![CDATA[{$title}]]></series_title>",
                "<series_type>{$type}</series_type>",
                "<series_episodes>{$episodes}</series_episodes>",
                '<my_id>0</my_id>',
                "<my_watched_episodes>{$watched}</my_watched_episodes>",
                '<my_start_date>0000-00-00</my_start_date>',
                '<my_finish_date>0000-00-00</my_finish_date>',
                '<my_rated></my_rated>',
                "<my_score>{$entry->score}</my_score>",
                '<my_storage></my_storage>',
                '<my_storage_value>0.00</my_storage_value>',
                '<my_status>' . $this->escape($entry->status) . '</my_status>',
                '<my_comments><![CDATA[]]></my_comments>',
                '<my_times_watched>0</my_times_watched>',
                '<my_rewatch_value></my_rewatch_value>',
                '<my_priority>LOW</my_priority>',
                '<my_tags><![CDATA[]]></my_tags>',
                '<my_rewatching>0</my_rewatching>',
                '<my_rewatching_ep>0</my_rewatching_ep>',
                '<my_discuss>1</my_discuss>',
                '<my_sns>default</my_sns>',
                '<update_on_import>1</update_on_import>',
                '</anime>',
            );
        }

        $lines[] = '</myanimelist>';

        // CRLF between lines, no newline after the last one (same as the original file)
        return implode("\r\n", $lines);
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
