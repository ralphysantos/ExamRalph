<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table = 'login_attempt';
    protected $fillable = [
        'ip', 'email', 'attempt','lock_time',
    ];
}
