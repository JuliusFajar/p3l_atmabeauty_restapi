<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perawatan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'perawatan';
    protected $primaryKey = 'id_perawatan';

    protected $fillable = [
        'nama_perawatan',
        'keterangan_perawatan',
        'syarat_perawatan',
        'harga_perawatan',
        'gambar_perawatan',
        'poin_perawatan',
    ];

    public function transaksi()
    {
        return $this->belongsToMany(Transaksi::class, 'transaksi_perawatan', 'id_perawatan', 'id_transaksi');
    }
}
