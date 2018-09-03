<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Gig extends Model
{
    protected $table = 'gigs';

    use Sluggable;

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'nama_gig'
            ]
        ];
    }

    protected $fillable = [
        'id','nama_gig','deskripsi','photo_gig','lokasi','aktif','detail_lokasi','lat','lng','tanggal_mulai','tanggal_selesai','status','type_gig','user_id','slug'
    ];
}
