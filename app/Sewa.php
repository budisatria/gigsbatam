<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    protected $table = 'sewas';

    protected $fillable = [
        'id','total_biaya','gig_id','object_id','subject_id','status','status_request','type_sewa'
    ];
}
