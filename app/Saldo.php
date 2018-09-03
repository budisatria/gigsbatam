<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table = 'saldo';

    protected $fillable = [
        'id','saldo','subject_id','type_pemilik'
    ];
}
