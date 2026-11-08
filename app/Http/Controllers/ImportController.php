<?php

namespace App\Http\Controllers;

use App\Models\ImportSession;
use App\Services\BatchParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImportController extends Controller
{
    public function create()
    {
        return view('imports.create');
    }

    public function store(Request $request, BatchParser $parser)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:txt', 'max:2048'],
        ]);

        $file = $request->file('file');
        $content = ltrim(file_get_contents($file->getRealPath()), "\xEF\xBB\xBF"); // strip BOM

        // same as splitter.py: stop reading at the first line containing "Manga"
        $kept = [];
        foreach (preg_split('/\R/', $content) as $line) {
            if (str_contains(trim($line), 'Manga')) {
                break;
            }
            $kept[] = $line;
        }

        $titles = $parser->parse(implode("\n", $kept));

        if (empty($titles)) {
            return back()->withErrors(['file' => 'No anime titles found in that file.']);
        }

        $session = DB::transaction(function () use ($file, $titles) {
            $session = ImportSession::create(['filename' => $file->getClientOriginalName()]);

            $session->entries()->createMany(
                collect($titles)->map(fn($title, $i) => [
                    'position' => $i,
                    'original_title' => $title,
                ])->all()
            );

            return $session;
        });

        return redirect()->route('imports.show', $session);
    }

    public function show(ImportSession $session)
    {
        $total = $session->entries()->count();

        $entry = $session->entries()
            ->where('position', $session->current_index)
            ->first();

        return view('imports.show', compact('session', 'entry', 'total'));
    }
}
