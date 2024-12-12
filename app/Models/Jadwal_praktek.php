<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal_praktek extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'jadwal_praktek';

    protected $fillable = [
        'id_jadwal',
        'id_pegawai',
        'shift'
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
}
