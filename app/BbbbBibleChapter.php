<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbBibleChapter extends Model
{
    protected $table = 'bbbb_bible_chapters';

    protected $guarded = [];

    protected $casts = [
        'verses' => 'array',
    ];

    public function book()
    {
        return $this->belongsTo(BbbbBibleBook::class, 'book_id');
    }

    public function bibleVerses()
    {
        return $this->hasMany(BbbbBibleVerse::class, 'chapter_id')->orderBy('verse_number');
    }
}
