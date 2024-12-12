<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function tampilLaporanCustomer(Request $request)
    {
        $year = $request->input('year'); // Tahun diambil dari request

        // Mendapatkan daftar tahun yang tersedia berdasarkan data customer
        $years = DB::table('customer')
            ->select(DB::raw('YEAR(tanggal_registrasi) as year'))
            ->groupBy(DB::raw('YEAR(tanggal_registrasi)'))
            ->get()
            ->pluck('year')
            ->toArray();  // Mengambil semua tahun yang ada

        // Mengambil data customer baru berdasarkan tahun dan mengelompokkannya per bulan
        $customerList = DB::table('customer') // Nama tabel disesuaikan
            ->select(
                DB::raw('MONTH(tanggal_registrasi) as month'),
                DB::raw('SUM(CASE WHEN jenis_kelamin = "L" THEN 1 ELSE 0 END) as total_pria'),
                DB::raw('SUM(CASE WHEN jenis_kelamin = "P" THEN 1 ELSE 0 END) as total_wanita'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal_registrasi', $year) // Filter berdasarkan tahun registrasi
            ->groupBy('month')
            ->orderBy('month') // Urutkan berdasarkan bulan
            ->get();

        // Inisialisasi data bulanan untuk setiap bulan dari Januari hingga Desember
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = [
                'bulan' => date("F", mktime(0, 0, 0, $month, 1)), // Nama bulan
                'pria' => 0,
                'wanita' => 0,
                'jumlah' => 0,
            ];
        }

        // Memasukkan data customer baru ke dalam array $monthlyData
        foreach ($customerList as $customer) {
            $month = $customer->month;
            $monthlyData[$month] = [
                'bulan' => date("F", mktime(0, 0, 0, $month, 1)),
                'pria' => (int)$customer->total_pria,
                'wanita' => (int)$customer->total_wanita,
                'jumlah' => (int)$customer->total,
            ];
        }

        // Menghitung total keseluruhan customer baru
        $annualTotal = array_reduce($monthlyData, function ($carry, $month) {
            return $carry + $month['jumlah'];
        }, 0);

        // Mengembalikan respons JSON dengan data yang terstruktur
        return response()->json([
            'message' => 'Laporan Customer Baru Tahunan',
            'tahun' => $year,
            'data' => array_values($monthlyData), // Mengubah array menjadi nilai berurutan
            'total_tahunan' => $annualTotal,
            'years' => $years // Kembalikan daftar tahun yang ada
        ], 200);
    }





    // public function tampilLaporanPendapatan(Request $request)
    // {
    //     $year = $request->input('year'); // Tahun diambil dari request

    //     // Mengambil data pendapatan dari transaksi perawatan dengan status "Pembayaran Sukses"
    //     $perawatanList = DB::table('transaksi_perawatan')
    //         ->join('transaksi', 'transaksi_perawatan.id_transaksi', '=', 'transaksi.id_transaksi')
    //         ->select(
    //             DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
    //             DB::raw('SUM(transaksi.nominal_transaksi) as total_perawatan')
    //         )
    //         ->whereYear('transaksi.tanggal_transaksi', $year)
    //         ->where('transaksi.status_transaksi', 'Pembayaran Sukses') // Kondisi status transaksi
    //         ->groupBy('month')
    //         ->get();

    //     // Mengambil data pendapatan dari transaksi produk dengan status "Pembayaran Sukses"
    //     $produkList = DB::table('transaksi_produk')
    //         ->join('transaksi', 'transaksi_produk.id_transaksi', '=', 'transaksi.id_transaksi')
    //         ->select(
    //             DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
    //             DB::raw('SUM(transaksi.nominal_transaksi) as total_produk')
    //         )
    //         ->whereYear('transaksi.tanggal_transaksi', $year)
    //         ->where('transaksi.status_transaksi', 'Pembayaran Sukses') // Kondisi status transaksi
    //         ->groupBy('month')
    //         ->get();

    //     // Inisialisasi data bulanan dengan nilai awal 0 untuk setiap bulan
    //     $monthlyData = [];
    //     for ($month = 1; $month <= 12; $month++) {
    //         $monthlyData[$month] = [
    //             'bulan' => date("F", mktime(0, 0, 0, $month, 1)),
    //             'perawatan' => 0,
    //             'produk' => 0,
    //             'total' => 0,
    //         ];
    //     }

    //     // Memasukkan data pendapatan perawatan ke dalam array $monthlyData
    //     foreach ($perawatanList as $perawatan) {
    //         $month = $perawatan->month;
    //         $monthlyData[$month]['perawatan'] = $perawatan->total_perawatan;
    //         $monthlyData[$month]['total'] += $perawatan->total_perawatan;
    //     }

    //     // Memasukkan data pendapatan produk ke dalam array $monthlyData
    //     foreach ($produkList as $produk) {
    //         $month = $produk->month;
    //         $monthlyData[$month]['produk'] = $produk->total_produk;
    //         $monthlyData[$month]['total'] += $produk->total_produk;
    //     }

    //     // Menghitung total tahunan
    //     $annualTotal = array_reduce($monthlyData, function ($carry, $month) {
    //         return $carry + $month['total'];
    //     }, 0);

    //     // Mengembalikan respons JSON dengan data yang terstruktur
    //     return response()->json([
    //         'message' => 'Laporan Pendapatan Tahunan',
    //         'tahun' => $year,
    //         'data' => array_values($monthlyData),
    //         'total_tahunan' => $annualTotal
    //     ], 200);
    // }

    public function tampilLaporanPendapatan(Request $request)
{
    $year = $request->input('year'); // Tahun diambil dari request

    // Mengambil data tahun yang tersedia berdasarkan transaksi
    $yearsAvailable = DB::table('transaksi')
        ->select(DB::raw('YEAR(tanggal_transaksi) as year'))
        ->groupBy(DB::raw('YEAR(tanggal_transaksi)'))
        ->get()
        ->pluck('year')
        ->toArray(); // Mengambil tahun-tahun yang tersedia dalam data transaksi

    // Jika tidak ada tahun yang tersedia, mengembalikan respons kosong
    if (empty($yearsAvailable)) {
        return response()->json([
            'message' => 'Tidak ada data pendapatan.',
            'years' => [],
            'data' => [],
            'total_tahunan' => 0
        ], 200);
    }

    // Jika tahun tidak dipilih, menggunakan tahun yang paling terbaru
    if (!$year) {
        $year = max($yearsAvailable); // Mengambil tahun terbaru dari yang tersedia
    }

    // Mengambil data pendapatan dari transaksi perawatan dengan status "Pembayaran Sukses"
    $perawatanList = DB::table('transaksi_perawatan')
        ->join('transaksi', 'transaksi_perawatan.id_transaksi', '=', 'transaksi.id_transaksi')
        ->select(
            DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
            DB::raw('SUM(transaksi.nominal_transaksi) as total_perawatan')
        )
        ->whereYear('transaksi.tanggal_transaksi', $year)
        ->where('transaksi.status_transaksi', 'Pembayaran Sukses') // Kondisi status transaksi
        ->groupBy('month')
        ->get();

    // Mengambil data pendapatan dari transaksi produk dengan status "Pembayaran Sukses"
    $produkList = DB::table('transaksi_produk')
        ->join('transaksi', 'transaksi_produk.id_transaksi', '=', 'transaksi.id_transaksi')
        ->select(
            DB::raw('MONTH(transaksi.tanggal_transaksi) as month'),
            DB::raw('SUM(transaksi.nominal_transaksi) as total_produk')
        )
        ->whereYear('transaksi.tanggal_transaksi', $year)
        ->where('transaksi.status_transaksi', 'Pembayaran Sukses') // Kondisi status transaksi
        ->groupBy('month')
        ->get();

    // Inisialisasi data bulanan dengan nilai awal 0 untuk setiap bulan
    $monthlyData = [];
    for ($month = 1; $month <= 12; $month++) {
        $monthlyData[$month] = [
            'bulan' => date("F", mktime(0, 0, 0, $month, 1)),
            'perawatan' => 0,
            'produk' => 0,
            'total' => 0,
        ];
    }

    // Memasukkan data pendapatan perawatan ke dalam array $monthlyData
    foreach ($perawatanList as $perawatan) {
        $month = $perawatan->month;
        $monthlyData[$month]['perawatan'] = $perawatan->total_perawatan;
        $monthlyData[$month]['total'] += $perawatan->total_perawatan;
    }

    // Memasukkan data pendapatan produk ke dalam array $monthlyData
    foreach ($produkList as $produk) {
        $month = $produk->month;
        $monthlyData[$month]['produk'] = $produk->total_produk;
        $monthlyData[$month]['total'] += $produk->total_produk;
    }

    // Menghitung total tahunan
    $annualTotal = array_reduce($monthlyData, function ($carry, $month) {
        return $carry + $month['total'];
    }, 0);

    // Mengembalikan respons JSON dengan data yang terstruktur
    return response()->json([
        'message' => 'Laporan Pendapatan Tahunan',
        'years' => $yearsAvailable, // Daftar tahun yang ada dalam data
        'tahun' => $year,
        'data' => array_values($monthlyData),
        'total_tahunan' => $annualTotal
    ], 200);
}

  
  public function getPerawatanTerlaris(Request $request)
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
            ->join('transaksi_perawatan', 'transaksi.id_transaksi', '=', 'transaksi_perawatan.id_transaksi')
            ->join('perawatan', 'transaksi_perawatan.id_perawatan', '=', 'perawatan.id_perawatan')
            ->select(
                'perawatan.id_perawatan',
                'perawatan.nama_perawatan as nama_perawatan',
                'perawatan.harga_perawatan as harga_perawatan', // Kolom harga
                DB::raw('COUNT(transaksi_perawatan.id_perawatan) as jumlah_pembelian') // Jumlah pembelian
            )
            ->whereYear('transaksi.tanggal_transaksi', $tahun)
            ->whereMonth('transaksi.tanggal_transaksi', $bulan)
            ->groupBy('perawatan.id_perawatan', 'perawatan.nama_perawatan', 'perawatan.harga_perawatan') // Grup dengan harga
            ->orderBy('jumlah_pembelian', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getCustomerPerDokter(Request $request)
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
            ->join('transaksi_perawatan', 'transaksi.id_transaksi', '=', 'transaksi_perawatan.id_transaksi')
            ->join('perawatan', 'transaksi_perawatan.id_perawatan', '=', 'perawatan.id_perawatan')
            ->select(
                'perawatan.id_perawatan',
                'perawatan.nama_perawatan as nama_perawatan',
                'perawatan.harga_perawatan as harga_perawatan', // Kolom harga
                DB::raw('COUNT(transaksi_perawatan.id_perawatan) as jumlah_pembelian') // Jumlah pembelian
            )
            ->whereYear('transaksi.tanggal_transaksi', $tahun)
            ->whereMonth('transaksi.tanggal_transaksi', $bulan)
            ->groupBy('perawatan.id_perawatan', 'perawatan.nama_perawatan', 'perawatan.harga_perawatan') // Grup dengan harga
            ->orderBy('jumlah_pembelian', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function getJumlahCustomerPerDokter($bulan, $tahun)
    {
        try {
            // Query untuk mendapatkan jumlah customer per dokter dan perawatan
            $data = DB::table('transaksi as t')
                ->join('customer as c', 't.id_customer', '=', 'c.id_customer')
                ->join('pegawai as p', 't.id_dokter', '=', 'p.id_pegawai')
                ->join('transaksi_perawatan as tp', 't.id_transaksi', '=', 'tp.id_transaksi') // Join dengan transaksi_perawatan
                ->join('perawatan as pr', 'tp.id_perawatan', '=', 'pr.id_perawatan') // Join dengan tabel perawatan
                ->select(
                    'p.nama_pegawai as dokter',
                    'pr.nama_perawatan as nama_perawatan', // Ambil nama perawatan dari tabel perawatan
                    DB::raw('COUNT(DISTINCT t.id_customer) as jumlah_customer'),
                    DB::raw('SUM(COUNT(DISTINCT t.id_customer)) OVER (PARTITION BY p.id_pegawai) as total_per_dokter')
                )
                ->whereMonth('t.tanggal_transaksi', '=', $bulan)
                ->whereYear('t.tanggal_transaksi', '=', $tahun)
                ->whereNotNull('t.id_dokter') // Pastikan transaksi terkait dengan dokter
                ->groupBy('p.nama_pegawai', 'pr.nama_perawatan') // Group by dokter dan nama perawatan
                ->orderBy('p.nama_pegawai', 'asc')
                ->orderBy('pr.nama_perawatan', 'asc')
                ->get();

            // Format data agar sesuai dengan format yang diinginkan
            $laporan = [];
            foreach ($data as $item) {
                $laporan[$item->dokter][] = [
                    'nama_perawatan' => $item->nama_perawatan,
                    'jumlah_customer' => $item->jumlah_customer,
                ];
            }

            // Mengembalikan data dalam format JSON
            return response()->json([
                'status' => 'success',
                'data' => $laporan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}
