<?php

namespace App\Helpers\Auth;

use App\Models\TokenLog;


trait HasTokenLog
{
    public function createToken($fcm_token = null): TokenLog
    {
        return TokenLog::login($this->id, static::class, $fcm_token);
    }


    public function deleteToken($fcm_token = null): void
    {
        TokenLog::logout($this->id, static::class, $fcm_token);
    }


}
