<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbDailyBreadPost extends Model
{
    protected $table = 'bbbb_daily_bread_posts';

    protected $guarded = [];

    protected $casts = [
        'share_date' => 'date:Y-m-d',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(BbbbUser::class, 'user_id');
    }

    public function passages()
    {
        return $this->hasMany(BbbbDailyBreadPassage::class, 'daily_bread_post_id')->orderBy('sort_order');
    }

    public function comments()
    {
        return $this->hasMany(BbbbDailyBreadComment::class, 'daily_bread_post_id')->orderBy('created_at');
    }

    public function reactions()
    {
        return $this->hasMany(BbbbDailyBreadReaction::class, 'daily_bread_post_id');
    }
}
