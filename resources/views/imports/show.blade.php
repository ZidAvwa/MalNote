<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Review - {{ $session->filename }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #1e1e1e;
            color: #d4d4d4;
            margin: 0;
            padding: 16px 24px;
        }

        .top {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .progress {
            color: #4fc1ff;
            font-weight: bold;
            font-size: 18px;
            margin-right: auto;
        }

        input[type=text] {
            background: #3c3c3c;
            color: #fff;
            border: 0;
            padding: 9px;
            font-size: 15px;
        }

        #search-input {
            width: 320px;
        }

        button {
            border: 0;
            color: #fff;
            cursor: pointer;
            padding: 10px 16px;
            font-size: 14px;
        }

        .main {
            display: flex;
            gap: 20px;
        }

        #results {
            flex: 3;
            list-style: none;
            margin: 0;
            padding: 0;
            background: #252526;
            min-height: 320px;
            max-height: 420px;
            overflow-y: auto;
        }

        #results li {
            padding: 10px;
            cursor: pointer;
        }

        #results li:hover {
            background: #2a2d2e;
        }

        #results li.active {
            background: #094771;
        }

        .detail {
            flex: 2;
            text-align: center;
        }

        #cover {
            width: 200px;
            height: 290px;
            object-fit: cover;
            background: #252526;
        }

        #final-title {
            width: 100%;
            text-align: center;
            font-weight: bold;
            margin: 8px 0;
        }

        #details {
            color: #aaa;
            white-space: pre-line;
            min-height: 40px;
        }

        .choices {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin: 10px 0;
        }

        .choices input {
            display: none;
        }

        .choices label {
            background: #333;
            color: #ccc;
            padding: 8px 10px;
            cursor: pointer;
            min-width: 110px;
            text-align: center;
            font-size: 13px;
        }

        .choices input:checked+label {
            background: #0e639c;
            color: #fff;
        }

        .ratings label {
            min-width: 130px;
        }

        .ratings input:checked+label {
            background: #094771;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 18px;
        }

        .back {
            background: #d97706;
        }

        .submit {
            background: #16a34a;
        }

        .skip {
            background: #dc2626;
        }

        .save {
            background: #2563eb;
        }
    </style>
</head>

<body>
    <p><a href="{{ route('imports.index') }}" style="color:#4fc1ff;">← All imports</a></p>
    
    @if ($entry)
        <div class="top">
            <div class="progress">Progress: {{ $session->current_index + 1 }}/{{ $total }}</div>
            <input type="text" id="search-input" value="{{ $entry->original_title }}">
            <button id="search-btn" style="background:#0e639c;">Search API</button>
        </div>

        <div class="main">
            <ul id="results"></ul>

            <div class="detail">
                <img id="cover" alt="">
                <input type="text" id="final-title">
                <div id="details"></div>
            </div>
        </div>

        <div class="choices" id="status-group">
            @foreach (['Completed', 'Watching', 'On-Hold', 'Plan to Watch', 'Dropped'] as $st)
                <input type="radio" name="status" id="st-{{ $loop->index }}" value="{{ $st }}"
                    @checked($st === $entry->status)>
                <label for="st-{{ $loop->index }}">{{ $st }}</label>
            @endforeach
        </div>

        <div class="choices ratings">
            <input type="radio" name="score" id="sc-0" value="0" @checked((int) $entry->score === 0)>
            <label for="sc-0">0 (Unrated)</label>
            @foreach (['Appalling', 'Horrible', 'Very Bad', 'Bad', 'Average', 'Fine', 'Good', 'Very Good', 'Great', 'Masterpiece'] as $i => $label)
                <input type="radio" name="score" id="sc-{{ $i + 1 }}" value="{{ $i + 1 }}"
                    @checked((int) $entry->score === $i + 1)>
                <label for="sc-{{ $i + 1 }}">({{ $i + 1 }}) {{ $label }}</label>
            @endforeach
        </div>

        <div class="actions">
            <button class="back" id="back-btn">◄ Go Back</button>
            <button class="submit" id="submit-btn">Submit &amp; Next</button>
            <button class="skip" id="skip-btn">Skip Title</button>
            <button class="save" id="save-btn">Save &amp; Exit</button>
        </div>

        <script>
            const searchUrl = @json(route('search'));
            let results = [];
            let selected = null;

            const $ = (id) => document.getElementById(id);

            async function runSearch() {
                const q = $('search-input').value.trim();
                if (!q) return;

                $('results').innerHTML = '';
                $('details').textContent = 'Searching API...';
                selected = null;

                try {
                    const res = await fetch(searchUrl + '?q=' + encodeURIComponent(q), {
                        headers: {
                            'Accept': 'application/json'
                        },
                    });
                    if (!res.ok) throw new Error('bad response');
                    results = await res.json();
                } catch (e) {
                    $('details').textContent = 'Search failed. Try again.';
                    return;
                }

                if (results.length === 0) {
                    $('details').textContent = 'No results found.';
                    return;
                }

                results.forEach((r, i) => {
                    const li = document.createElement('li');
                    li.textContent = `${r.title} (${r.type}, ${r.year})`;
                    li.addEventListener('click', () => select(i));
                    $('results').appendChild(li);
                });
                select(0);
            }

            function select(i) {
                selected = results[i];
                document.querySelectorAll('#results li').forEach((li, n) => li.classList.toggle('active', n === i));
                $('final-title').value = selected.title;
                $('details').textContent = `Type: ${selected.type}\nScore: ${selected.score}`;
                $('cover').src = selected.image_url || '';
            }

            $('search-btn').addEventListener('click', runSearch);
            $('search-input').addEventListener('keydown', (e) => {
                if (e.key === 'Enter') runSearch();
            });

            $('submit-btn').addEventListener('click', async () => {
                if (!selected) {
                    alert('Pick a search result first.');
                    return;
                }

                const ok = await post(urls.submit, {
                    mal_id: selected.mal_id,
                    final_title: $('final-title').value.trim(),
                    series_type: selected.type,
                    series_episodes: selected.episodes || 0,
                    score: document.querySelector('input[name=score]:checked').value,
                    status: document.querySelector('input[name=status]:checked').value,
                });
                if (ok) location.reload();
            });

            $('skip-btn').addEventListener('click', async () => {
                if (await post(urls.skip)) location.reload();
            });

            $('save-btn').addEventListener('click', () => {
                window.location = urls.export;
            });

            runSearch(); // auto-search the current title on load
        </script>
    @else
        <h1>{{ $session->filename }}</h1>
        <p>All {{ $total }} titles reviewed.</p>
        <button id="back-btn" style="background:#d97706; color:#fff; border:0; padding:10px 16px; cursor:pointer;">◄ Go
            Back</button>
        <a href="{{ route('imports.export', $session) }}"
            style="background:#2563eb; color:#fff; padding:10px 16px; text-decoration:none;">Download XML</a>
    @endif

    <script>
        const urls = {
            submit: @json(route('imports.submit', $session)),
            skip: @json(route('imports.skip', $session)),
            back: @json(route('imports.back', $session)),
            export: @json(route('imports.export', $session)),
        };

        async function post(url, body = {}) {
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify(body),
                });
                if (!res.ok) throw new Error('bad response');
                return true;
            } catch (e) {
                alert('Something went wrong. Try again.');
                return false;
            }
        }

        const backBtn = document.getElementById('back-btn');
        if (backBtn) {
            backBtn.addEventListener('click', async () => {
                if (await post(urls.back)) location.reload();
            });
        }
    </script>
</body>

</html>
