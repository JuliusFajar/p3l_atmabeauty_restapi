<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'harga_produk',
        'gambar_produk',
        'stock_produk',
        'keterangan_produk',
    ];

    public function transaksi()
    {
        return $this->belongsToMany(Transaksi::class, 'transaksi_produk', 'id_produk', 'id_transaksi');
    }
}
