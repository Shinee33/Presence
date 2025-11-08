<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    protected $table = 'pengajuan_izin';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'tgl_izin',
        'status',
        'keterangan',
        'status_approved',
    ];
}
