<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbBibleVersion extends Model
{
    protected $table = 'bbbb_bible_versions';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function books()
    {
        return $this->hasMany(BbbbBibleBook::class, 'version_id')->orderBy('sort_order');
    }
}
