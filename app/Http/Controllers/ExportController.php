<?php

namespace App\Http\Controllers;

use App\Models\ImportSession;
use App\Services\MalXmlBuilder;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    public function __invoke(ImportSession $session, MalXmlBuilder $builder): Response
    {
        // "Anime.txt" => "mal_Anime.xml", same naming as your Python
        $name = 'mal_' . pathinfo($session->filename, PATHINFO_FILENAME) . '.xml';

        return response($builder->build($session), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
        ]);
    }
}