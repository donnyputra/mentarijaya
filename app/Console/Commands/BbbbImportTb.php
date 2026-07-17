<?php

namespace App\Console\Commands;

use App\BbbbBibleBook;
use App\BbbbBibleChapter;
use App\BbbbBibleVerse;
use App\BbbbBibleVersion;
use App\BbbbUser;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BbbbImportTb extends Command
{
    protected $signature = 'bbbb:import-tb
        {--source=https://raw.githubusercontent.com/godlytalias/Bible-Database/master/Indonesian/bible.json}
        {--force : Replace the existing TB import}
        {--seed-demo : Create the BBBB demo users}';

    protected $description = 'Import the BBBB Terjemahan Baru Bible pack from JSON into Laravel tables.';

    public function handle()
    {
        if ($this->option('seed-demo')) {
            $this->seedDemoUsers();
        }

        $existing = BbbbBibleVersion::where('code', 'tb')->first();
        $existingBookCount = $existing ? $existing->books()->count() : 0;
        if ($existing && !$this->option('force') && $existingBookCount >= 66) {
            BbbbBibleVersion::where('code', 'tb-lite')->update(['is_active' => false]);
            $this->info('TB already imported.');
            return 0;
        }
        $replaceExisting = $existing && ($this->option('force') || $existingBookCount > 0);

        $payload = $this->fetchPayload($this->option('source'));
        $books = isset($payload['Book']) ? $payload['Book'] : [];
        $canon = $this->canon();

        if (count($books) !== count($canon)) {
            $this->error('Expected '.count($canon).' books, got '.count($books).'.');
            return 1;
        }

        DB::transaction(function () use ($existing, $replaceExisting, $books, $canon) {
            if ($replaceExisting) {
                $existing->delete();
                $existing = null;
            }

            $version = $existing ?: BbbbBibleVersion::create([
                'code' => 'tb',
                'name' => 'Terjemahan Baru (TB)',
                'language' => 'id',
                'description' => 'Full Terjemahan Baru pack imported into the Laravel BBBB backend so the app can download and read it offline from our own API.',
                'size_label' => 'Full pack - backend managed',
                'is_active' => true,
            ]);

            if ($existing) {
                $version->forceFill([
                    'name' => 'Terjemahan Baru (TB)',
                    'language' => 'id',
                    'description' => 'Full Terjemahan Baru pack imported into the Laravel BBBB backend so the app can download and read it offline from our own API.',
                    'size_label' => 'Full pack - backend managed',
                    'is_active' => true,
                ])->save();
            }

            BbbbBibleVersion::where('code', 'tb-lite')->update(['is_active' => false]);

            foreach ($books as $bookIndex => $bookData) {
                $bookMeta = $canon[$bookIndex];
                $book = BbbbBibleBook::create([
                    'version_id' => $version->id,
                    'external_id' => $bookMeta['external_id'],
                    'name' => $bookMeta['name'],
                    'sort_order' => $bookIndex + 1,
                ]);

                $chapters = isset($bookData['Chapter']) ? $bookData['Chapter'] : [];
                foreach ($chapters as $chapterIndex => $chapterData) {
                    $verses = [];
                    $verseRows = [];
                    $sourceVerses = isset($chapterData['Verse']) ? $chapterData['Verse'] : [];

                    foreach ($sourceVerses as $verseIndex => $verseData) {
                        $text = $this->cleanVerseText(isset($verseData['Verse']) ? $verseData['Verse'] : '');
                        $verses[] = $text;
                        $verseRows[] = [
                            'verse_number' => $verseIndex + 1,
                            'text' => $text,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    $chapter = BbbbBibleChapter::create([
                        'book_id' => $book->id,
                        'external_id' => $bookMeta['external_id'].'-'.($chapterIndex + 1),
                        'book_external_id' => $bookMeta['external_id'],
                        'book_name' => $bookMeta['name'],
                        'chapter_number' => $chapterIndex + 1,
                        'summary' => $this->buildSummary($verses),
                        'verses' => $verses,
                    ]);

                    foreach ($verseRows as $row) {
                        $row['chapter_id'] = $chapter->id;
                        BbbbBibleVerse::create($row);
                    }
                }
            }
        });

        $this->info('TB import completed.');
        return 0;
    }

    private function fetchPayload($source)
    {
        $client = new Client(['timeout' => 120]);
        $response = $client->get($source);

        return json_decode((string) $response->getBody(), true);
    }

    private function seedDemoUsers()
    {
        $this->seedUser('test@a.b', 'Demo BBBB Brother', '1985-07-17');
        $this->seedUser('brother@bbbb.app', 'Pak Markus', '1980-03-21');
    }

    private function seedUser($email, $fullName, $dateOfBirth)
    {
        $user = BbbbUser::where('email', $email)->first();
        if ($user) {
            return;
        }

        BbbbUser::create([
            'full_name' => $fullName,
            'email' => $email,
            'password' => Hash::make('asdfasdf'),
            'date_of_birth' => $dateOfBirth,
            'timezone' => 'Asia/Jakarta',
            'is_active' => true,
        ]);
    }

    private function cleanVerseText($text)
    {
        return trim(rtrim(trim($text), '"'));
    }

    private function buildSummary(array $verses)
    {
        if (count($verses) === 0) {
            return 'Imported chapter from the backend-managed TB pack.';
        }

        $summary = $verses[0];
        if (count($verses) > 1) {
            $summary .= ' '.$verses[1];
        }

        return strlen($summary) > 180 ? substr($summary, 0, 177).'...' : $summary;
    }

    private function canon()
    {
        return [
            ['external_id' => 'genesis', 'name' => 'Kejadian'],
            ['external_id' => 'exodus', 'name' => 'Keluaran'],
            ['external_id' => 'leviticus', 'name' => 'Imamat'],
            ['external_id' => 'numbers', 'name' => 'Bilangan'],
            ['external_id' => 'deuteronomy', 'name' => 'Ulangan'],
            ['external_id' => 'joshua', 'name' => 'Yosua'],
            ['external_id' => 'judges', 'name' => 'Hakim-Hakim'],
            ['external_id' => 'ruth', 'name' => 'Rut'],
            ['external_id' => '1-samuel', 'name' => '1 Samuel'],
            ['external_id' => '2-samuel', 'name' => '2 Samuel'],
            ['external_id' => '1-kings', 'name' => '1 Raja-raja'],
            ['external_id' => '2-kings', 'name' => '2 Raja-raja'],
            ['external_id' => '1-chronicles', 'name' => '1 Tawarikh'],
            ['external_id' => '2-chronicles', 'name' => '2 Tawarikh'],
            ['external_id' => 'ezra', 'name' => 'Ezra'],
            ['external_id' => 'nehemiah', 'name' => 'Nehemia'],
            ['external_id' => 'esther', 'name' => 'Ester'],
            ['external_id' => 'job', 'name' => 'Ayub'],
            ['external_id' => 'psalms', 'name' => 'Mazmur'],
            ['external_id' => 'proverbs', 'name' => 'Amsal'],
            ['external_id' => 'ecclesiastes', 'name' => 'Pengkhotbah'],
            ['external_id' => 'song-of-songs', 'name' => 'Kidung Agung'],
            ['external_id' => 'isaiah', 'name' => 'Yesaya'],
            ['external_id' => 'jeremiah', 'name' => 'Yeremia'],
            ['external_id' => 'lamentations', 'name' => 'Ratapan'],
            ['external_id' => 'ezekiel', 'name' => 'Yehezkiel'],
            ['external_id' => 'daniel', 'name' => 'Daniel'],
            ['external_id' => 'hosea', 'name' => 'Hosea'],
            ['external_id' => 'joel', 'name' => 'Yoel'],
            ['external_id' => 'amos', 'name' => 'Amos'],
            ['external_id' => 'obadiah', 'name' => 'Obaja'],
            ['external_id' => 'jonah', 'name' => 'Yunus'],
            ['external_id' => 'micah', 'name' => 'Mikha'],
            ['external_id' => 'nahum', 'name' => 'Nahum'],
            ['external_id' => 'habakkuk', 'name' => 'Habakuk'],
            ['external_id' => 'zephaniah', 'name' => 'Zefanya'],
            ['external_id' => 'haggai', 'name' => 'Hagai'],
            ['external_id' => 'zechariah', 'name' => 'Zakharia'],
            ['external_id' => 'malachi', 'name' => 'Maleakhi'],
            ['external_id' => 'matthew', 'name' => 'Matius'],
            ['external_id' => 'mark', 'name' => 'Markus'],
            ['external_id' => 'luke', 'name' => 'Lukas'],
            ['external_id' => 'john', 'name' => 'Yohanes'],
            ['external_id' => 'acts', 'name' => 'Kisah Para Rasul'],
            ['external_id' => 'romans', 'name' => 'Roma'],
            ['external_id' => '1-corinthians', 'name' => '1 Korintus'],
            ['external_id' => '2-corinthians', 'name' => '2 Korintus'],
            ['external_id' => 'galatians', 'name' => 'Galatia'],
            ['external_id' => 'ephesians', 'name' => 'Efesus'],
            ['external_id' => 'philippians', 'name' => 'Filipi'],
            ['external_id' => 'colossians', 'name' => 'Kolose'],
            ['external_id' => '1-thessalonians', 'name' => '1 Tesalonika'],
            ['external_id' => '2-thessalonians', 'name' => '2 Tesalonika'],
            ['external_id' => '1-timothy', 'name' => '1 Timotius'],
            ['external_id' => '2-timothy', 'name' => '2 Timotius'],
            ['external_id' => 'titus', 'name' => 'Titus'],
            ['external_id' => 'philemon', 'name' => 'Filemon'],
            ['external_id' => 'hebrews', 'name' => 'Ibrani'],
            ['external_id' => 'james', 'name' => 'Yakobus'],
            ['external_id' => '1-peter', 'name' => '1 Petrus'],
            ['external_id' => '2-peter', 'name' => '2 Petrus'],
            ['external_id' => '1-john', 'name' => '1 Yohanes'],
            ['external_id' => '2-john', 'name' => '2 Yohanes'],
            ['external_id' => '3-john', 'name' => '3 Yohanes'],
            ['external_id' => 'jude', 'name' => 'Yudas'],
            ['external_id' => 'revelation', 'name' => 'Wahyu'],
        ];
    }
}
