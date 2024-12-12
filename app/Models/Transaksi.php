<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_customer',
        'id_pegawai',
        'id_dokter',
        'id_kasir',
        'id_beautician',
        'id_promo',
        'tanggal_transaksi',
        'jenis_transaksi',
        'status_transaksi',
        'nominal_transaksi',
        'keluhan',
        'nomor_transaksi',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo');  // asumsikan kolom kunci asing adalah 'customer_id'
    }

    // Relasi dengan perawatan
    // public function perawatan()
    // {
    //     return $this->hasMany(Transaksi_perawatan::class, 'id_transaksi', 'id_transaksi');
    // }

    // // Relasi dengan produk
    // public function produk()
    // {
    //     return $this->hasMany(Transaksi_produk::class, 'id_transaksi', 'id_transaksi');
    // }
    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'transaksi_produk', 'id_transaksi', 'id_produk');
    }

    // Relasi ke Perawatan
    public function perawatan()
    {
        return $this->belongsToMany(Perawatan::class, 'transaksi_perawatan', 'id_transaksi', 'id_perawatan');
    }

}
