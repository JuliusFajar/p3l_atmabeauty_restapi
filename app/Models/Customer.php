<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $timestamps = false;
    protected $table = 'customer';
    protected $primaryKey = 'id_customer';

    protected $fillable = [
        'nomor_customer',
        'nama_customer',
        'username',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat_customer',
        'nomor_telepon',
        'email_customer',
        'alergi_obat',
        'poin_customer',
        'tanggal_registrasi',
        'password',
        'profile_customer',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
