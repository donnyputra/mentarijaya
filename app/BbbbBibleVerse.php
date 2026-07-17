<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbBibleVerse extends Model
{
    protected $table = 'bbbb_bible_verses';

    protected $guarded = [];

    public function chapter()
    {
        return $this->belongsTo(BbbbBibleChapter::class, 'chapter_id');
    }
}
