<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class CombosSucPYC extends Model
{
    protected $table = 'pyc_cmb_suc';
    public $timestamps = false;
}
