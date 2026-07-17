<?php

namespace App\Http\Controllers\Api;

use App\BbbbBibleChapter;
use App\BbbbBibleVersion;
use App\BbbbDailyBreadComment;
use App\BbbbDailyBreadPassage;
use App\BbbbDailyBreadPost;
use App\BbbbDailyBreadReaction;
use App\BbbbReadingLog;
use App\BbbbUser;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BbbbController extends Controller
{
    private $reactionKinds = ['amen', 'praying', 'thankful'];

    public function health()
    {
        return response()->json(['status' => 'ok']);
    }

    public function login(Request $request)
    {
        $email = Str::lower(trim($request->input('email', '')));
        $password = $request->input('password', '');

        if ($email === '' || $password === '') {
            return $this->error('email and password are required', 400);
        }

        $user = BbbbUser::where('email', $email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->error('invalid email or password', 401);
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json([
            'token' => $user->createToken('bbbb-mobile')->plainTextToken,
            'user' => $this->formatUser($user),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($this->formatUser($request->user()));
    }

    public function listBibleVersions()
    {
        $versions = BbbbBibleVersion::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($versions);
    }

    public function getBibleVersion($versionCode)
    {
        $version = BbbbBibleVersion::with(['books.chapters'])
            ->where('code', $versionCode)
            ->where('is_active', true)
            ->first();

        if (!$version) {
            return $this->error('bible version not found', 404);
        }

        return response()->json($this->formatBibleVersion($version));
    }

    public function getTodayReadingLogs(Request $request)
    {
        $day = $this->parseDate($request->query('date'));
        if (!$day) {
            return $this->error('invalid date', 400);
        }

        $versionCode = trim($request->query('version_code', ''));
        if ($versionCode === '') {
            return $this->error('version_code is required', 400);
        }

        $logs = BbbbReadingLog::with('bibleChapter')
            ->join('bbbb_bible_chapters', 'bbbb_bible_chapters.id', '=', 'bbbb_reading_logs.bible_chapter_id')
            ->join('bbbb_bible_books', 'bbbb_bible_books.id', '=', 'bbbb_bible_chapters.book_id')
            ->where('bbbb_reading_logs.user_id', $request->user()->id)
            ->where('bbbb_reading_logs.version_code', $versionCode)
            ->whereDate('bbbb_reading_logs.read_on', $day->toDateString())
            ->orderBy('bbbb_bible_books.sort_order')
            ->orderBy('bbbb_bible_chapters.chapter_number')
            ->select('bbbb_reading_logs.*')
            ->get();

        return response()->json($this->formatReadingLogs($day, $versionCode, $logs));
    }

    public function replaceTodayReadingLogs(Request $request)
    {
        $day = $this->parseDate($request->input('date'));
        if (!$day) {
            return $this->error('invalid date', 400);
        }

        $versionCode = trim($request->input('version_code', ''));
        if ($versionCode === '') {
            return $this->error('version_code is required', 400);
        }

        $chapterExternalIds = $request->input('chapter_external_ids', []);
        if (!is_array($chapterExternalIds)) {
            return $this->error('chapter_external_ids must be an array', 400);
        }

        $chapters = collect();
        if (count($chapterExternalIds) > 0) {
            $chapters = $this->chaptersForVersion($versionCode)
                ->whereIn('bbbb_bible_chapters.external_id', $chapterExternalIds)
                ->orderBy('bbbb_bible_books.sort_order')
                ->orderBy('bbbb_bible_chapters.chapter_number')
                ->get();

            if ($chapters->count() !== count(array_unique($chapterExternalIds))) {
                return $this->error('one or more chapter_external_ids were not found', 400);
            }
        }

        DB::transaction(function () use ($request, $versionCode, $day, $chapters) {
            BbbbReadingLog::where('user_id', $request->user()->id)
                ->where('version_code', $versionCode)
                ->whereDate('read_on', $day->toDateString())
                ->delete();

            foreach ($chapters as $chapter) {
                BbbbReadingLog::create([
                    'user_id' => $request->user()->id,
                    'version_code' => $versionCode,
                    'bible_chapter_id' => $chapter->id,
                    'read_on' => $day->toDateString(),
                ]);
            }
        });

        $logs = BbbbReadingLog::with('bibleChapter')
            ->where('user_id', $request->user()->id)
            ->where('version_code', $versionCode)
            ->whereDate('read_on', $day->toDateString())
            ->get();

        return response()->json($this->formatReadingLogs($day, $versionCode, $logs));
    }

    public function listPosts(Request $request)
    {
        $posts = $this->postQuery()
            ->where('user_id', $request->user()->id)
            ->orderBy('share_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($posts->map(function ($post) use ($request) {
            return $this->formatPost($post, $request->user()->id, true);
        })->values());
    }

    public function feed(Request $request)
    {
        $posts = $this->postQuery()
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($posts->map(function ($post) use ($request) {
            return $this->formatPost($post, $request->user()->id, true);
        })->values());
    }

    public function createPost(Request $request)
    {
        $day = $this->parseDate($request->input('share_date'));
        if (!$day) {
            return $this->error('invalid date', 400);
        }

        $versionCode = trim($request->input('version_code', ''));
        if ($versionCode === '') {
            return $this->error('version_code is required', 400);
        }

        $versionExists = BbbbBibleVersion::where('code', $versionCode)
            ->where('is_active', true)
            ->exists();
        if (!$versionExists) {
            return $this->error('version_code was not found', 400);
        }

        $reflection = trim($request->input('reflection_text', ''));
        $takeaway = trim($request->input('takeaway_text', ''));
        if ($reflection === '' && $takeaway === '') {
            return $this->error('reflection_text or takeaway_text is required', 400);
        }

        $status = strtolower(trim($request->input('status', 'draft')));
        if (!in_array($status, ['draft', 'published'], true)) {
            return $this->error('status must be draft or published', 400);
        }

        $passageInputs = $this->normalizePassageInputs($request);
        if (count($passageInputs) === 0) {
            return $this->error('chapter_external_ids or passages is required', 400);
        }

        $resolvedPassages = $this->resolvePassages($versionCode, $passageInputs);
        if (isset($resolvedPassages['error'])) {
            return $this->error($resolvedPassages['error'], 400);
        }

        $post = DB::transaction(function () use ($request, $day, $versionCode, $reflection, $takeaway, $status, $resolvedPassages) {
            $post = BbbbDailyBreadPost::create([
                'user_id' => $request->user()->id,
                'share_date' => $day->toDateString(),
                'version_code' => $versionCode,
                'reflection_text' => $reflection,
                'takeaway_text' => $takeaway,
                'status' => $status,
                'published_at' => $status === 'published' ? now() : null,
            ]);

            foreach ($resolvedPassages as $passage) {
                $passage['daily_bread_post_id'] = $post->id;
                BbbbDailyBreadPassage::create($passage);
            }

            return $this->postQuery()->find($post->id);
        });

        return response()->json($this->formatPost($post, $request->user()->id, true), 201);
    }

    public function publishPost(Request $request, $postId)
    {
        $post = BbbbDailyBreadPost::where('id', $postId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$post) {
            return $this->error('post not found', 404);
        }

        if ($post->status !== 'published') {
            $post->forceFill([
                'status' => 'published',
                'published_at' => now(),
            ])->save();
        }

        $post = $this->postQuery()->find($post->id);

        return response()->json($this->formatPost($post, $request->user()->id, true));
    }

    public function listComments(Request $request, $postId)
    {
        $post = $this->postQuery()->find($postId);
        if (!$post) {
            return $this->error('post not found', 404);
        }
        if ($post->status !== 'published' && $post->user_id !== $request->user()->id) {
            return $this->error('comments are only available for published posts', 403);
        }

        return response()->json($post->comments->map(function ($comment) {
            return $this->formatComment($comment);
        })->values());
    }

    public function createComment(Request $request, $postId)
    {
        $body = trim($request->input('body', ''));
        if ($body === '') {
            return $this->error('body is required', 400);
        }

        $post = BbbbDailyBreadPost::find($postId);
        if (!$post) {
            return $this->error('post not found', 404);
        }
        if ($post->status !== 'published') {
            return $this->error('comments can only be added to published posts', 403);
        }

        $comment = BbbbDailyBreadComment::create([
            'daily_bread_post_id' => $post->id,
            'user_id' => $request->user()->id,
            'body' => $body,
        ])->load('user');

        return response()->json($this->formatComment($comment), 201);
    }

    public function setReaction(Request $request, $postId, $kind)
    {
        $kind = strtolower(trim($kind));
        if (!in_array($kind, $this->reactionKinds, true)) {
            return $this->error('reaction kind must be one of amen, praying, thankful', 400);
        }

        $post = $this->postQuery()->find($postId);
        if (!$post) {
            return $this->error('post not found', 404);
        }
        if ($post->status !== 'published' && $post->user_id !== $request->user()->id) {
            return $this->error('reactions are only available for published posts', 403);
        }

        if ($request->boolean('active')) {
            BbbbDailyBreadReaction::firstOrCreate([
                'daily_bread_post_id' => $post->id,
                'user_id' => $request->user()->id,
                'kind' => $kind,
            ]);
        } else {
            BbbbDailyBreadReaction::where('daily_bread_post_id', $post->id)
                ->where('user_id', $request->user()->id)
                ->where('kind', $kind)
                ->delete();
        }

        $post = $this->postQuery()->find($post->id);

        return response()->json($this->formatPost($post, $request->user()->id, false));
    }

    private function chaptersForVersion($versionCode)
    {
        return BbbbBibleChapter::query()
            ->join('bbbb_bible_books', 'bbbb_bible_books.id', '=', 'bbbb_bible_chapters.book_id')
            ->join('bbbb_bible_versions', 'bbbb_bible_versions.id', '=', 'bbbb_bible_books.version_id')
            ->where('bbbb_bible_versions.code', $versionCode)
            ->where('bbbb_bible_versions.is_active', true)
            ->select('bbbb_bible_chapters.*');
    }

    private function normalizePassageInputs(Request $request)
    {
        $passages = $request->input('passages', []);
        if (is_array($passages) && count($passages) > 0) {
            return $passages;
        }

        $chapterIds = $request->input('chapter_external_ids', []);
        if (!is_array($chapterIds)) {
            return [];
        }

        return array_map(function ($chapterId) {
            return ['chapter_external_id' => $chapterId];
        }, $chapterIds);
    }

    private function resolvePassages($versionCode, array $inputs)
    {
        $chapterIds = [];
        foreach ($inputs as $input) {
            $chapterId = trim(isset($input['chapter_external_id']) ? $input['chapter_external_id'] : '');
            if ($chapterId === '') {
                return ['error' => 'chapter_external_id is required for every passage'];
            }
            $chapterIds[] = $chapterId;
        }

        $chapters = $this->chaptersForVersion($versionCode)
            ->whereIn('bbbb_bible_chapters.external_id', $chapterIds)
            ->get()
            ->keyBy('external_id');

        if ($chapters->count() !== count(array_unique($chapterIds))) {
            return ['error' => 'one or more passages were not found for the version'];
        }

        $resolved = [];
        foreach ($inputs as $index => $input) {
            $chapter = $chapters->get($input['chapter_external_id']);
            $verses = $chapter->verses ?: [];
            $startVerse = isset($input['start_verse']) ? (int) $input['start_verse'] : null;
            $endVerse = isset($input['end_verse']) ? (int) $input['end_verse'] : null;

            if ($startVerse !== null && ($startVerse < 1 || $startVerse > count($verses))) {
                return ['error' => 'start_verse is out of range for '.$chapter->external_id];
            }
            if ($endVerse !== null && ($endVerse < 1 || $endVerse > count($verses))) {
                return ['error' => 'end_verse is out of range for '.$chapter->external_id];
            }
            if ($startVerse !== null && $endVerse !== null && $endVerse < $startVerse) {
                return ['error' => 'end_verse must be greater than or equal to start_verse'];
            }

            $resolved[] = [
                'bible_chapter_id' => $chapter->id,
                'chapter_external_id' => $chapter->external_id,
                'version_code' => $versionCode,
                'reference_label' => $this->formatReference($chapter->book_name, $chapter->chapter_number, $startVerse, $endVerse),
                'book_name' => $chapter->book_name,
                'chapter_number' => $chapter->chapter_number,
                'start_verse' => $startVerse,
                'end_verse' => $endVerse,
                'sort_order' => $index + 1,
            ];
        }

        return $resolved;
    }

    private function postQuery()
    {
        return BbbbDailyBreadPost::with(['user', 'passages', 'comments.user', 'reactions']);
    }

    private function parseDate($raw)
    {
        if (!$raw) {
            return Carbon::today();
        }

        try {
            return Carbon::createFromFormat('Y-m-d', substr($raw, 0, 10))->startOfDay();
        } catch (\Exception $exception) {
            return null;
        }
    }

    private function formatReadingLogs(Carbon $day, $versionCode, $logs)
    {
        return [
            'date' => $day->toDateString(),
            'version_code' => $versionCode,
            'chapter_external_ids' => $logs->map(function ($log) {
                return $log->bibleChapter ? $log->bibleChapter->external_id : null;
            })->filter()->values(),
            'logs' => $logs,
        ];
    }

    private function formatBibleVersion(BbbbBibleVersion $version)
    {
        return [
            'id' => $version->id,
            'code' => $version->code,
            'name' => $version->name,
            'language' => $version->language,
            'description' => $version->description,
            'size_label' => $version->size_label,
            'is_active' => $version->is_active,
            'books' => $version->books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'version_id' => $book->version_id,
                    'external_id' => $book->external_id,
                    'name' => $book->name,
                    'sort_order' => $book->sort_order,
                    'chapters' => $book->chapters->map(function ($chapter) {
                        return [
                            'id' => $chapter->id,
                            'book_id' => $chapter->book_id,
                            'external_id' => $chapter->external_id,
                            'book_external_id' => $chapter->book_external_id,
                            'book_name' => $chapter->book_name,
                            'chapter_number' => $chapter->chapter_number,
                            'summary' => $chapter->summary,
                            'verses' => $chapter->verses ?: [],
                        ];
                    })->values(),
                ];
            })->values(),
        ];
    }

    private function formatPost(BbbbDailyBreadPost $post, $currentUserId, $includeComments)
    {
        $reactionSummaries = [];
        foreach ($this->reactionKinds as $kind) {
            $reactions = $post->reactions->where('kind', $kind);
            $reactionSummaries[] = [
                'kind' => $kind,
                'count' => $reactions->count(),
                'reacted' => $reactions->where('user_id', $currentUserId)->count() > 0,
            ];
        }

        $response = [
            'id' => $post->id,
            'share_date' => optional($post->share_date)->toDateString(),
            'version_code' => $post->version_code,
            'reflection_text' => $post->reflection_text,
            'takeaway_text' => $post->takeaway_text,
            'status' => $post->status,
            'published_at' => optional($post->published_at)->toJSON(),
            'author' => $this->formatAuthor($post->user),
            'passages' => $post->passages,
            'comment_count' => $post->comments->count(),
            'reaction_summaries' => $reactionSummaries,
            'created_at' => optional($post->created_at)->toJSON(),
            'updated_at' => optional($post->updated_at)->toJSON(),
        ];

        if ($includeComments) {
            $response['comments'] = $post->comments->map(function ($comment) {
                return $this->formatComment($comment);
            })->values();
        }

        return $response;
    }

    private function formatComment(BbbbDailyBreadComment $comment)
    {
        return [
            'id' => $comment->id,
            'body' => $comment->body,
            'author' => $this->formatAuthor($comment->user),
            'created_at' => optional($comment->created_at)->toJSON(),
            'updated_at' => optional($comment->updated_at)->toJSON(),
        ];
    }

    private function formatAuthor($user)
    {
        return [
            'id' => $user ? $user->id : null,
            'email' => $user ? $user->email : null,
            'full_name' => $user ? $user->full_name : null,
        ];
    }

    private function formatUser(BbbbUser $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'full_name' => $user->full_name,
            'date_of_birth' => optional($user->date_of_birth)->toDateString(),
            'timezone' => $user->timezone,
            'is_active' => (bool) $user->is_active,
            'last_login_at' => optional($user->last_login_at)->toJSON(),
            'created_at' => optional($user->created_at)->toJSON(),
            'updated_at' => optional($user->updated_at)->toJSON(),
        ];
    }

    private function formatReference($bookName, $chapterNumber, $startVerse, $endVerse)
    {
        if ($startVerse === null) {
            return $bookName.' '.$chapterNumber;
        }
        if ($endVerse !== null && $endVerse > $startVerse) {
            return $bookName.' '.$chapterNumber.':'.$startVerse.'-'.$endVerse;
        }

        return $bookName.' '.$chapterNumber.':'.$startVerse;
    }

    private function error($message, $status)
    {
        return response()->json(['error' => $message], $status);
    }
}
