<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;

class PDFController extends Controller
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
                'satuan' => $usage->satuan,
                'digunakan' => $usage->digunakan
            ];
        }

        // Mengembalikan respons berhasil dengan data yang terstruktur
        return response()->json([
            'message' => 'Data penggunaan bahan baku per produk ditemukan',
            'data' => $groupedData
        ], 200);
    }
}
