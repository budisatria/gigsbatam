<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'banks';

    protected $fillable = [
        'id','nama_bank','no_rek','atas_nama','cabang'
    ];
}
