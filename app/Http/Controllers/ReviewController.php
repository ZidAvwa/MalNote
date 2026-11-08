<?php

namespace App\Http\Controllers;

use App\Models\ImportSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ReviewController extends Controller
{
    public function submit(Request $request, ImportSession $session): JsonResponse
    {
        $data = $request->validate([
            'mal_id'      => ['required', 'integer'],
            'final_title' => ['required', 'string', 'max:255'],
            'score'       => ['required', 'integer', 'between:0,10'],
            'status'      => ['required', Rule::in(['Completed', 'Watching', 'On-Hold', 'Plan to Watch', 'Dropped'])],
            'series_type'     => ['nullable', 'string', 'max:20'],
            'series_episodes' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['series_episodes'] ??= 0;
        $entry = $this->currentEntry($session);
        abort_if(! $entry, 404);

        $entry->update($data + ['skipped' => false]);
        $this->advance($session);

        return response()->json(['ok' => true]);
    }

    public function skip(ImportSession $session): JsonResponse
    {
        $entry = $this->currentEntry($session);
        abort_if(! $entry, 404);

        // clear any earlier submission, so a skipped title never ends up in the export
        $entry->update([
            'skipped' => true,
            'mal_id' => null,
            'final_title' => null,
            'series_type' => null,
            'series_episodes' => 0,
        ]);
        $this->advance($session);

        return response()->json(['ok' => true]);
    }

    public function back(ImportSession $session): JsonResponse
    {
        if ($session->current_index > 0) {
            $session->decrement('current_index');
        }

        return response()->json(['ok' => true]);
    }

    private function currentEntry(ImportSession $session)
    {
        return $session->entries()->where('position', $session->current_index)->first();
    }

    private function advance(ImportSession $session): void
    {
        $total = $session->entries()->count();

        $session->update([
            'current_index' => min($session->current_index + 1, $total),
        ]);
    }
}
