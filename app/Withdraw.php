<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $table = 'withdraw';

    protected $fillable = [
        'id','jumlah','saldo_id'
    ];
}
