<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pegawai'; 
    // Gunakan 'id' sebagai primary key, bukan 'nama_lengkap'
    protected $primaryKey = 'id';
    protected $fillable = [
    'id',
    'nama_lengkap',
    'jabatan',
    'no_hp',
    'email',
    'foto_profil',
    'status',
    'kode_dept',
    'password',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }   
}