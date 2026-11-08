<?php

namespace App\Services;

use App\Models\ImportSession;

class MalXmlBuilder
{
    public function build(ImportSession $session): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
        $xml .= "<myanimelist>\n<myinfo>\n";
        $xml .= '<user_id>' . $this->escape($session->mal_user_id) . "</user_id>\n";
        $xml .= '<user_name>' . $this->escape($session->mal_user_name) . "</user_name>\n";
        $xml .= "<user_export_type>1</user_export_type>\n</myinfo>\n";

        $entries = $session->entries()
            ->where('skipped', false)
            ->whereNotNull('mal_id')
            ->get(); // already ordered by position

        foreach ($entries as $entry) {
            // "]]>" would end the CDATA block early, so split it
            $title = str_replace(']]>', ']]]]><![CDATA[>', $entry->final_title);

            $xml .= "<anime>\n";
            $xml .= "<series_animedb_id>{$entry->mal_id}</series_animedb_id>\n";
            $xml .= "<series_title><![CDATA[{$title}]]></series_title>\n";
            $xml .= "<my_score>{$entry->score}</my_score>\n";
            $xml .= '<my_status>' . $this->escape($entry->status) . "</my_status>\n";
            $xml .= "<update_on_import>1</update_on_import>\n";
            $xml .= "</anime>\n";
        }

        return $xml . '</myanimelist>';
    }

    private function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}