<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonfirmasiPembayaran extends Model
{
    protected $table = 'konfirmasi_pembayarans';

    protected $fillable = [
        'id','nama_rek','no_rek','nama_bank','photo','sewa_id','bank_admin_id'
    ];}
