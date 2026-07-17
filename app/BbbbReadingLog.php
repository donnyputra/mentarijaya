<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbReadingLog extends Model
{
    protected $table = 'bbbb_reading_logs';

    protected $guarded = [];

    protected $casts = [
        'read_on' => 'date:Y-m-d',
    ];

    public function bibleChapter()
    {
        return $this->belongsTo(BbbbBibleChapter::class, 'bible_chapter_id');
    }

    public function user()
    {
        return $this->belongsTo(BbbbUser::class, 'user_id');
    }
}
