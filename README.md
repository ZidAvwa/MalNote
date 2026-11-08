# MAL List Importer (MalNote)

A Laravel web app that turns a plain text anime list into a MyAnimeList import file (XML).

Upload your list, review each title against AniList search results, pick a status and rating, and download an XML you can import into MyAnimeList.

## Features

- Upload a `.txt` list; titles like `Naruto S1.S2` or `Naruto M1` are expanded into separate searches
- Auto-searches AniList for each title, with a manual search box for corrections
- Pick a status and a rating (0 to 10) for every title, or skip it
- Go back and change an earlier answer
- Progress is saved, so you can close the tab and continue later
- Past imports page with continue, download and delete
- Exports the same XML format as the original Python version

## Requirements

- PHP 8.2 or newer
- Composer
- No Node.js needed

## Setup

```bash
git clone https://github.com/ZidAvwa/MalNote.git
cd MalNote
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Then open http://localhost:8000.

The default database is SQLite. If `php artisan migrate` asks to create `database/database.sqlite`, answer yes.

On macOS or Linux, use `cp .env.example .env` instead of `copy`.

## How to use

1. Enter your MAL username and numeric user id. The id is in the `<user_id>` tag of an XML file exported from MAL (myanimelist.net/panel.php?go=export).
2. Choose your `.txt` list and click **Upload & Start**.
3. For each title, check the search result, edit the title if needed, choose a status and rating, then click **Submit & Next** (or **Skip Title**).
4. Click **Save & Exit** or **Download XML** to get `mal_<filename>.xml`.
5. Import it at myanimelist.net/import.php with the type "MyAnimeList Import".

## List file format

One title per line:

```
Attack on Titan
Naruto S1.S2.S3
Your Name M1
```

- `S2` means "Season 2" (`S1` adds nothing), `M1` means "Movie 1". Combine them with dots: `S1.S2.M1`.
- Blank lines and lines starting with `[` or `#` are ignored.
- Reading stops at the first line that contains `Manga`.
- `LiveAction` is removed from the search text unless the line also contains `S1`.

## Tests

```bash
php artisan test
```

## Project structure

| Path | Purpose |
|---|---|
| `app/Services/AniListService.php` | AniList search with timeout and retries |
| `app/Services/BatchParser.php` | Turns the uploaded text into search titles |
| `app/Services/MalXmlBuilder.php` | Builds the MAL XML export |
| `app/Http/Controllers/` | Import, review, search and export controllers |
| `resources/views/imports/` | Upload, review and past imports pages |

## Notes

- Search results come from AniList and are cached for 10 minutes. Titles without a MAL id are hidden.
- `.env` and the SQLite database are not committed.