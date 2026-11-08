<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Past imports</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1e1e1e; color: #d4d4d4; margin: 0; padding: 24px; }
        a { color: #4fc1ff; }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #333; }
        th { color: #aaa; font-weight: normal; }
        .badge { padding: 3px 8px; font-size: 12px; }
        .done { background: #16a34a; color: #fff; }
        .progress { background: #d97706; color: #fff; }
        .btn { display: inline-block; padding: 7px 12px; color: #fff; text-decoration: none; font-size: 13px; }
        .btn-blue { background: #0e639c; }
        .btn-green { background: #16a34a; }
    </style>
</head>

<body>
    <h1>Past imports</h1>
    <a class="btn btn-green" href="{{ route('imports.create') }}">+ New import</a>

    @if ($sessions->isEmpty())
        <p>No imports yet.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>File</th>
                    <th>MAL user</th>
                    <th>Reviewed</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    @php $finished = $session->current_index >= $session->entries_count; @endphp
                    <tr>
                        <td>{{ $session->filename }}</td>
                        <td>{{ $session->mal_user_name }}</td>
                        <td>{{ $session->current_index }} / {{ $session->entries_count }}</td>
                        <td>{{ $session->submitted_count }}</td>
                        <td>
                            @if ($finished)
                                <span class="badge done">Finished</span>
                            @else
                                <span class="badge progress">In progress</span>
                            @endif
                        </td>
                        <td>{{ $session->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <a class="btn btn-blue" href="{{ route('imports.show', $session) }}">
                                {{ $finished ? 'Open' : 'Continue' }}
                            </a>
                            <a class="btn btn-green" href="{{ route('imports.export', $session) }}">Download XML</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>