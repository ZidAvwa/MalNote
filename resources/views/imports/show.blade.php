<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import #{{ $session->id }}</title>
</head>
<body style="font-family: Arial; background:#1e1e1e; color:#d4d4d4; padding:40px;">
    <h1>{{ $session->filename }}</h1>
    <p>{{ $session->entries_count }} titles loaded.</p>

    <ol>
        @foreach ($session->entries as $entry)
            <li>{{ $entry->original_title }}</li>
        @endforeach
    </ol>
</body>
</html>