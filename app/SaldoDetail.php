<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoDetail extends Model
{
    protected $table = 'saldo_detail';

    protected $fillable = [
        'id','saldo_id','sewa_id'
    ];

}
