<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupbandMusisi extends Model
{

	protected $table = 'grupband_musisi';

    protected $fillable = [
        'id','position_id','musician_id','grupband_id'
    ];

    public function musician(){
		return $this->belongsTo('App\Musician','musician_id');
	}
}
