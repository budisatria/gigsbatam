<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Grupband extends Model
{
    protected $table = 'grupbands';

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
                'source' => 'nama_grupband'
            ]
        ];
    }

    protected $fillable = [
        'id','nama_grupband','deskripsi','Wilayah','photo','cover','aktif','basis','harga','youtube_video','url_website','username_soundcloud','username_reverbnation','admin_id'
    ];
}
