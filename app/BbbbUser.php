<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class BbbbUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'bbbb_users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date:Y-m-d',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];
}
