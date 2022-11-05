<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\HasApiTokens;

class PersonalAccessTokens extends PersonalAccessToken 
{
    use HasApiTokens;

    protected $table = 'pyc_personal_access_tokens';
}
