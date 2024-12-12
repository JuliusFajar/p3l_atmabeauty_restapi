<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class PDFProdukController extends Controller
{
    public function showMonthlySalesReport()
    {
        // Mengambil data transaksi dan mengelompokkannya berdasarkan bulan
        $transaksiList = Transaksi::select(
            DB::raw('MONTH(tanggal_transaksi) as month'),
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(nominal_transaksi) as total_amount')
        )
            ->groupBy('month')
            ->get();

        // Jika tidak ada data transaksi, kirim respons kesalahan
        if ($transaksiList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data transaksi',
                'data' => null
            ], 404);
        }

        // Mengelompokkan data transaksi per bulan
        $monthlyData = [];
        foreach ($transaksiList as $transaksi) {
            $month = $transaksi->month;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [
                    'bulan' => date("F", mktime(0, 0, 0, $month, 1)), // Konversi angka bulan menjadi nama bulan
                    'jumlahTransaksi' => 0,
                    'jumlahUang' => 0
                ];
            }

            $monthlyData[$month]['jumlahTransaksi'] += $transaksi->total_transactions;
            $monthlyData[$month]['jumlahUang'] += $transaksi->total_amount;
        }

        // Mengembalikan respons berhasil dengan data yang terstruktur
        return response()->json([
            'message' => 'Data Transaksi Bulanan ditemukan',
            'data' => array_values($monthlyData)
        ], 200);
    }

    public function showUsageMaterialReport($startDate, $endDate)
    {
        // Mengambil data penggunaan bahan baku berdasarkan periode
        $materialUsageList = DB::table('penggunaan_bahan as b')
            ->join('produk as p', 'b.id_produk', '=', 'p.id_produk')
            ->select(
                'p.nama_produk',
                'b.nama_bahan',
                'b.satuan',
                DB::raw('SUM(b.jumlah) AS digunakan')
            )
            ->whereBetween('b.tanggal', [$startDate, $endDate])
            ->groupBy('p.nama_produk', 'b.nama_bahan', 'b.satuan')
            ->orderBy('p.nama_produk', 'asc')
            ->get();

        // Jika tidak ada data penggunaan bahan baku, kirim respons kesalahan
        if ($materialUsageList->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data penggunaan bahan baku untuk periode yang dipilih',
                'data' => null
            ], 404);
        }

        // Mengelompokkan data penggunaan bahan baku berdasarkan produk
        $groupedData = [];
        foreach ($materialUsageList as $usage) {
            $productName = $usage->nama_produk;
            if (!isset($groupedData[$productName])) {
                $groupedData[$productName] = [];
            }

            $groupedData[$productName][] = [
                'nama_bahan' => $usage->nama_bahan,
                'satuan' => $susage->satuan,
                'digunakan' => $usage->digunakan
            ];
        }

        // Mengembalikan respons berhasil dengan data yang terstruktur
        return response()->json([
            'message' => 'Data penggunaan bahan baku per produk ditemukan',
            'data' => $groupedData
        ], 200);
    }

    public function top10LatestSuccessfulTransactions()
{
    // Query untuk mengambil 10 transaksi terbaru dengan status "Pembayaran Sukses"
    $latestTransactions = DB::table('transaksi as t')
        // ->join('transaksi_produk', 'transaksi.id_transaksi', '=', 'transaksi_produk.id_transaksi')
        ->join('transaksi_produk as tp', 't.id_transaksi', '=', 'tp.id_transaksi') // Join dengan tabel transaksi_produk
        ->join('produk as p', 'tp.id_produk', '=', 'p.id_produk') // Join dengan tabel produk
        ->join('pegawai as pg', 't.id_pegawai', '=', 'pg.id_pegawai') // Join dengan tabel pegawai
        ->join('customer as c', 't.id_customer', '=', 'c.id_customer') // Join dengan tabel customer untuk mendapatkan nama_customer
        // ->join('produk as pr', 't.id_produk', '=', 'pr.id_produk')
        ->select(
            'tp.id_transaksi',
            't.tanggal_transaksi',
            't.nominal_transaksi',
            't.status_transaksi',
            'pg.nama_pegawai', // Nama pegawai yang terkait
            'c.nama_customer', // Nama customer yang terkait
            'p.harga_produk',
            'p.stock_produk',
            DB::raw('COUNT(transaksi_produk.id_produk) as jumlah_pembelian') 
        )
        ->where('t.status_transaksi', '=', 'Pembayaran Sukses') // Filter hanya transaksi dengan status "Pembayaran Sukses"
        ->groupBy('t.id_transaksi', 't.tanggal_transaksi', 't.nominal_transaksi', 't.status_transaksi', 'pg.nama_pegawai', 'c.nama_customer','p.harga_produk','p.stock_produk') // Kelompokkan per transaksi
        ->orderBy('jumlah_pembelian', 'desc')
        // ->orderBy('t.tanggal_transaksi', 'desc') // Urutkan dari yang terbaru
        ->limit(10) // Ambil hanya 10 transaksi
        ->get();

    // Jika tidak ada data, kirim respons kesalahan
    if ($latestTransactions->isEmpty()) {
        return response()->json([
            'message' => 'Tidak ada transaksi dengan status Pembayaran Sukses',
            'data' => null
        ], 404);
    }

    // Kembalikan respons berhasil
    return response()->json([
        'message' => '10 Transaksi Terbaru dengan Status Pembayaran Sukses ditemukan',
        'data' => $latestTransactions
    ], 200);
}
public function getProdukTerlaris(Request $request)
    {
        // Validasi input bulan dan tahun
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        // Query untuk mendapatkan produk terlaris
        $data = DB::table('transaksi')
            ->join('transaksi_produk', 'transaksi.id_transaksi', '=', 'transaksi_produk.id_transaksi')
            ->join('produk', 'transaksi_produk.id_produk', '=', 'produk.id_produk')
            ->select(
                'produk.id_produk',
                'produk.nama_produk as nama_produk',
                'produk.harga_produk as harga_produk', // Kolom harga
                'produk.stock_produk as stock_produk',
                DB::raw('COUNT(transaksi_produk.id_produk) as jumlah_pembelian') // Jumlah pembelian
            )
            ->whereYear('transaksi.tanggal_transaksi', $tahun)
            ->whereMonth('transaksi.tanggal_transaksi', $bulan)
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.harga_produk', 'produk.stock_produk') // Grup dengan harga
            ->orderBy('jumlah_pembelian', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    


}
