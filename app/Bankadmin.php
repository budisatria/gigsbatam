<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bankadmin extends Model
{
    protected $table = 'bank_admin';

    protected $fillable = [
        'id','nama_bank','no_rek','atas_nama','cabang'
    ];
}
