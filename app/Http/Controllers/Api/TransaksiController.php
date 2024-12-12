<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Pegawai;
use App\Models\Ruangan;
use App\Models\Transaksi_perawatan;
use App\Models\Transaksi_produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $transaksi = Transaksi::all();

        if ($transaksi->isNotEmpty()) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        // Cari transaksi berdasarkan id
        $transaksi = Transaksi::with(['pegawai', 'customer', 'promo']) // Pastikan Anda mengganti relasi 'kasir' dan 'beautician' sesuai model Anda.
            ->find($id);

        // Cek apakah transaksi ditemukan
        if ($transaksi) {
            return response([
                'message' => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ], 404);
    }
    
    public function showPegawai($id)
    {
        // Cari transaksi berdasarkan id
        $pegawai = Pegawai::find($id);

        // Cek apakah transaksi ditemukan
        if ($pegawai) {
            return response([
                'message' => 'Retrieve Pegawai Success',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 404);
    }

    public function showCustomer($id)
    {
        // Cari transaksi berdasarkan id
        $customer = Customer::find($id);

        // Cek apakah transaksi ditemukan
        if ($customer) {
            return response([
                'message' => 'Retrieve Customer Success',
                'data' => $customer
            ], 200);
        }

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ], 404);
    }


    public function inputDataPemeriksaan(Request $request, string $id)
    {
        $transaksi = Transaksi::find($id);
        if (is_null($transaksi)) {
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_beautician' => 'nullable',
            'id_ruangan' => 'nullable',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        // Update status ruangan menjadi "booked"
        $ruangan = Ruangan::find($storeData['id_ruangan']);
        if ($ruangan) {
            $ruangan->status = 'booked';
            $ruangan->save();
        }

        // Update status pegawai (beautician) menjadi "booked"
        $beautician = Pegawai::find($storeData['id_beautician']);
        if ($beautician) {
            $beautician->status_pegawai = 'booked';
            $beautician->save();
        }

        // Update transaksi dengan id_beautician dan id_ruangan yang diinputkan
        $transaksi->id_beautician = $storeData['id_beautician'];
        // $transaksi->id_ruangan = $storeData['id_ruangan'];

        $transaksi->status_transaksi = "Menunggu Kasir";
        if ($transaksi->save()) {
            return response([
                'message' => 'Update Pemeriksaan Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Update Pemeriksaan Fail',
            'data' => null
        ], 400);
    }
    public function bayarTransaksi(Request $request, string $id)
    {
        $transaksi = Transaksi::find($id);
        if (is_null($transaksi)) {
            return response([
                'message' => 'Transaksi Not Found',
                'data' => null
            ], 404);
        }

        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_promo' => 'nullable',
            'id_kasir' => 'required',
            'nominal_transaksi' => 'required',
            'penguranganPoin' => 'nullable',
            'penambahanPoin' => 'nullable',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 402);
        }

        if (isset($storeData['penguranganPoin']) && !is_null($storeData['penguranganPoin'])) {
            $customer = Customer::find($transaksi['id_customer']);
            if ($customer) {
                $customer->poin_customer = $customer->poin_customer - $storeData['penguranganPoin'];
                $customer->save();
            }
        }

        if (isset($storeData['penambahanPoin']) && !is_null($storeData['penambahanPoin'])) {
            $customer = Customer::find($transaksi['id_customer']);
            if ($customer) {
                $customer->poin_customer = $customer->poin_customer + $storeData['penambahanPoin'];
                $customer->save();
            }
        }

        // Cek apakah 'id_promo' ada dalam $storeData dan tidak null
        if (isset($storeData['id_promo']) && !is_null($storeData['id_promo'])) {
            $transaksi->id_promo = $storeData['id_promo'];
        }

        $transaksi->id_kasir = $storeData['id_kasir'];
        $transaksi->nominal_transaksi = $storeData['nominal_transaksi'];
        $transaksi->status_transaksi = "Pembayaran Sukses";

        if ($transaksi->save()) {
            return response([
                'message' => 'Pembayaran Berhasil!',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Pembayaran Gagal!',
            'data' => null
        ], 400);
    }



    // public function inputProduk(Request $request)
    // {
    //     $storeData = $request->all();
    //     $validate = Validator::make($storeData, [
    //         'id_produk' => 'nullable',
    //         'id_transaksi' => 'required',
    //     ]);

    //     if ($validate->fails()) {
    //         return response(['message' => $validate->errors()], 400);
    //     }

    //     $transaksi = Transaksi_produk::create($storeData);
    //     return response([
    //         'message' => 'input produk berhasil',
    //         'data' => $transaksi
    //     ], 200);
    // }

    public function inputProduk(Request $request)
    {
        try {
            $idTransaksi = $request->input('id_transaksi');
            $idProdukArray = $request->input('id_produk');

            // Pastikan data adalah array sebelum memasukkan ke database
            if (is_array($idProdukArray)) {
                // Hapus semua produk sebelumnya untuk id_transaksi yang sama
                DB::table('transaksi_produk')->where('id_transaksi', $idTransaksi)->delete();

                // Masukkan produk baru
                foreach ($idProdukArray as $idProduk) {
                    DB::table('transaksi_produk')->insert([
                        'id_transaksi' => $idTransaksi,
                        'id_produk' => $idProduk,
                    ]);
                }
            } else {
                return response()->json(['message' => 'id_produk harus berupa array'], 400);
            }

            return response()->json(['message' => 'Produk berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function inputPerawatan(Request $request)
    {
        try {
            $idTransaksi = $request->input('id_transaksi');
            $idPerawatanArray = $request->input('id_perawatan');

            // Pastikan data adalah array sebelum memasukkan ke database
            if (is_array($idPerawatanArray)) {
                // Hapus semua perawatan sebelumnya untuk id_transaksi yang sama
                DB::table('transaksi_perawatan')->where('id_transaksi', $idTransaksi)->delete();

                // Masukkan perawatan baru
                foreach ($idPerawatanArray as $idPerawatan) {
                    DB::table('transaksi_perawatan')->insert([
                        'id_transaksi' => $idTransaksi,
                        'id_perawatan' => $idPerawatan,
                    ]);
                }
            } else {
                return response()->json(['message' => 'id_perawatan harus berupa array'], 400);
            }

            return response()->json(['message' => 'Perawatan berhasil disimpan'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // public function showAntrianPerDokter($id)
    // {
    //     //
    //     $transaksi = Transaksi::all();

    //     if ($transaksi->isNotEmpty()) {
    //         $antrianDokter = Transaksi::where('id_dokter', $id)->get();
    //         $customerYangAntri = Customer::with(['transaksis.produk'])->find($id);
    //         return response([
    //             'message' => 'Retrieve All Success',
    //             'data' => $antrianDokter
    //         ], 200);
    //     }

    //     return response([
    //         'message' => 'Empty',
    //         'data' => null
    //     ], 400);
    // }

    public function showAntrianPerDokter($id)
    {
        // Mendapatkan transaksi dengan id_dokter tertentu dan memuat relasi customer
        $antrianDokter = Transaksi::where('id_dokter', $id)
            ->whereRaw('LOWER(status_transaksi) = ?', ['menunggu dokter'])
            ->with('customer') // Memuat relasi customer
            ->get(); // Mengambil semua data transaksi

        if ($antrianDokter->isNotEmpty()) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $antrianDokter
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }
    public function showRiwayatCustomer($id)
    {
        // Mendapatkan transaksi dengan id_dokter tertentu dan memuat relasi customer
        $riwayatCustomer = Transaksi::where('id_customer', $id)
            ->whereRaw('LOWER(status_transaksi) = ?', ['pembayaran sukses'])
            ->with('customer', 'perawatan', 'produk') // Memuat relasi customer
            ->get(); // Mengambil semua data transaksi

        if ($riwayatCustomer->isNotEmpty()) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $riwayatCustomer
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function transaksiBelumBayar()
    {
        // Memuat transaksi dengan relasi produk dan perawatan
        $transaksi = Transaksi::whereRaw('LOWER(status_transaksi) = ?', ['menunggu kasir'])
            ->with(['produk', 'perawatan', 'customer']) // Memuat relasi produk dan perawatan
            ->get();

        if ($transaksi->isNotEmpty()) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $transaksi
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'id_customer' => 'required',
            'id_pegawai' => 'required',
            'id_dokter' => 'nullable',
            'id_ruangan' => 'nullable',
            'id_beautician' => 'nullable',
            'jenis_transaksi' => 'required',
            'keluhan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ], 422);
        }

        try {
            // Proceed with customer ID generation and saving as before
            $currentDate = Carbon::now();
            $day = $currentDate->format('d');
            $month = $currentDate->format('m');
            $year = $currentDate->format('y');
            $lastTransaksi = Transaksi::orderBy('id_transaksi', 'desc')->first();
            $sequenceNumber = $lastTransaksi ? $lastTransaksi->id_transaksi + 1 : 1;
            $nomorTransaksi = "{$day}{$month}{$year}-{$sequenceNumber}";
            \Log::info('Generated Nomor Transaksi: ' . $nomorTransaksi);  // Log it for debugging

            $statusTransaksi = strtolower($request->jenis_transaksi === 'Perawatan dengan Konsultasi') || strtolower($request->jenis_transaksi === 'Pembelian Produk dengan Konsultasi')
                ? 'Menunggu Dokter' // Set status to "Menunggu Dokter"
                : 'Menunggu Kasir'; // Or any other default status if not

            if (strtolower($request->jenis_transaksi) === 'perawatan tanpa konsultasi') {
                // Proceed with the room status update
                $ruangan = Ruangan::find($request->id_ruangan);
                if ($ruangan) {
                    $ruangan->status = 'booked';
                    $ruangan->save();
                }
            }


            if ($request->id_beautician) {
                $beautician = Pegawai::find($request->id_beautician);
                if ($beautician) {
                    $beautician->status_pegawai = 'booked';
                    $beautician->save();
                }
            }

            // Create the customer with validated data
            $transaksi = Transaksi::create([
                'id_customer' => $request->id_customer,
                'id_dokter' => $request->id_dokter ?? null,
                'id_beautician' => $request->id_beautician ?? null,
                'id_pegawai' => $request->id_pegawai,
                'tanggal_transaksi' => $currentDate,
                'jenis_transaksi' => $request->jenis_transaksi,
                'status_transaksi' => $statusTransaksi,
                'keluhan' => $request->keluhan,
                'nomor_transaksi' => $nomorTransaksi,
            ]);

            return response()->json([
                "status" => true,
                "message" => 'Berhasil menambah transaksi',
                "data" => $transaksi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */

    public function showHistoryCustomer($id)
    {
        //
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaksitransaksis = Transaksitransaksi::where('id_transaksi', $id)->get();
        $bahanBakuDibutuhkan = [];

        foreach ($transaksitransaksis as $transaksitransaksi) {
            $transaksiId = $transaksitransaksi->id_transaksi;
            $jumlahPembelian = $transaksitransaksi->jumlah_pembelian;

            $resepBahanBakus = Resep_bahan_baku_transaksi::where('id_transaksi', $transaksiId)->get();

            foreach ($resepBahanBakus as $resepBahanBaku) {
                $bahanBaku = Bahan_Baku::find($resepBahanBaku->id_bahan_baku);
                $jumlahYangDibutuhkan = $resepBahanBaku->jumlah_bahan_baku * $jumlahPembelian;

                if (isset($bahanBakuDibutuhkan[$bahanBaku->id_bahan_baku])) {
                    $bahanBakuDibutuhkan[$bahanBaku->id_bahan_baku]['jumlah'] += $jumlahYangDibutuhkan;
                } else {
                    $bahanBakuDibutuhkan[$bahanBaku->id_bahan_baku] = [
                        'nama' => $bahanBaku->nama_bahan_baku,
                        'jumlah' => $jumlahYangDibutuhkan,
                        'satuan' => $bahanBaku->satuan
                    ];
                }
            }
        }

        return response()->json(array_values($bahanBakuDibutuhkan), 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        //
    }
}