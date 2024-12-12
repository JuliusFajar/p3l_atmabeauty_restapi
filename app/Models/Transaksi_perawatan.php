<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi_perawatan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'transaksi_perawatan';

    protected $fillable = [
        'id_perawatan',
        'id_transaksi',
    ];

    public function perawatan()
    {
        return $this->belongsTo(Perawatan::class, 'id_perawatan');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');  // asumsikan kolom kunci asing adalah 'customer_id'
    }
}
