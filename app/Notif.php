<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    protected $table = 'notif';

    protected $fillable = [
        'id','object_id','subject_id','user_id','type_user','type_notif','type_subject','baca'
    ];
}
