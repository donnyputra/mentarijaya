<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BbbbDailyBreadComment extends Model
{
    protected $table = 'bbbb_daily_bread_comments';

    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(BbbbDailyBreadPost::class, 'daily_bread_post_id');
    }

    public function user()
    {
        return $this->belongsTo(BbbbUser::class, 'user_id');
    }
}
