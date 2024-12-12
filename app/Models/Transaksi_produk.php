<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi_produk extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transaksi_produk';

    protected $fillable = [
        'id_produk',
        'id_transaksi',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
}
