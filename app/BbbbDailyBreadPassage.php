<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbDailyBreadPassage extends Model
{
    protected $table = 'bbbb_daily_bread_passages';

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(BbbbDailyBreadPost::class, 'daily_bread_post_id');
    }

    public function bibleChapter()
    {
        return $this->belongsTo(BbbbBibleChapter::class, 'bible_chapter_id');
    }
}
