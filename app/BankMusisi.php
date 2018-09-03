<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankMusisi extends Model
{
    protected $table = 'bank_musisi';

    protected $fillable = [
        'id','musician_id','bank_id'
    ];
}
