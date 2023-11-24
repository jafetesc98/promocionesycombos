<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CombosDetPYC extends Model
{
    protected $table = 'pyc_invdcm';
    public $timestamps = false;
}
