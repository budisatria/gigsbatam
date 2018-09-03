<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Cviebrock\EloquentSluggable\Sluggable;

class Musician extends Authenticatable
{
    use Sluggable;

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $table = 'musicians';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','email','firebase','password','deskripsi', 'harga_sewa','aktif', 'basis', 'no_telp', 'Wilayah', 'photo', 'youtube_video', 'url_website', 'username_soundcloud', 'username_reverbnation',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function grupbandmusisi(){
        return $this->hasMany('App\GrupbandMusisi');
    }
}
