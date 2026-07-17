<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbBibleBook extends Model
{
    protected $table = 'bbbb_bible_books';

    protected $guarded = [];

    public function version()
    {
        return $this->belongsTo(BbbbBibleVersion::class, 'version_id');
    }

    public function chapters()
    {
        return $this->hasMany(BbbbBibleChapter::class, 'book_id')->orderBy('chapter_number');
    }
}
