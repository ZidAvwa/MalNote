<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MAL List Importer</title>
</head>

<body style="font-family: Arial; background:#1e1e1e; color:#d4d4d4; padding:40px;">
    <h1>MAL List Importer</h1>

    <p><a href="{{ route('imports.index') }}" style="color:#4fc1ff;">View past imports</a></p>
    
    @if ($errors->any())
        <p style="color:#f87171;">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('imports.store') }}" enctype="multipart/form-data">
        @csrf

        <p>
            <label>MAL username<br>
                <input type="text" name="mal_user_name" value="{{ old('mal_user_name') }}" required>
            </label>
        </p>

        <p>
            <label>MAL user id (numbers only)<br>
                <input type="text" name="mal_user_id" value="{{ old('mal_user_id') }}" inputmode="numeric" required>
            </label>
        </p>

        <p>
            <input type="file" name="file" accept=".txt" required>
        </p>

        <button type="submit">Upload & Start</button>
    </form>
</body>

</html>
