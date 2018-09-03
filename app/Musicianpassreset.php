<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Musicianpassreset extends Model
{
    protected $table = 'password_reset_musicians';

    protected $fillable = [
        'id','email','token'
    ];
}
