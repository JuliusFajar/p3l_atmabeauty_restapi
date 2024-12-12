<?php

use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\api\CustomerController;
use App\Http\Controllers\api\RuanganController;
use App\Http\Controllers\api\PegawaiController;
use App\Http\Controllers\api\JadwalController;
use App\Http\Controllers\api\JadwalPraktekController;
use App\Http\Controllers\api\PerawatanController;
use App\Http\Controllers\api\PromoController;
use App\Http\Controllers\api\ProdukController;
use App\Http\Controllers\api\LaporanController;
use App\Http\Controllers\Api\PDFController;
use App\Http\Controllers\Api\PDFProdukController;

use App\Models\Customer;
use App\Models\Ruangan;
use App\Models\Pegawai;
use App\Models\Jadwal;
use App\Models\Jadwal_Praktek;
use App\Models\Perawatan;
use App\Models\Promo;
use App\Models\Produk;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);

Route::post('mobile/loginMo', [App\Http\Controllers\Api\AuthControllerMo::class, 'login']);


Route::resource('customer', CustomerController::class);

Route::resource('Jadwal', JadwalController::class);

Route::get('/searchCustomer/{email}', [CustomerController::class, 'searchByEmail']);
Route::post('/customer/generate-id', [CustomerController::class, 'generateNoCustomer']);

Route::apiResource('customer', CustomerController::class);
Route::apiResource('jadwal', JadwalController::class);
Route::apiResource('jadwal_Praktek', JadwalPraktekController::class);
Route::apiResource('ruangan', RuanganController::class);

//Route::post('/customer', [CustomerController::class, 'store']);

//route pegawai
Route::get('/pegawai', [PegawaiController::class, 'index']);           
Route::post('/pegawai', [PegawaiController::class, 'store']);         
Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);       
Route::put('/pegawai/{id}', [PegawaiController::class, 'update']);     
Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy']);  
Route::get('/pegawai/search/{name}', [PegawaiController::class, 'searchByName']);
//Route::get('/pegawai/search/{jabatan}', [PegawaiController::class, 'searchByJabatan']);
Route::get('/searchByJabatan/{jabatan}', [PegawaiController::class, 'searchByJabatan']);
Route::get('/pegawai/{id}', [PegawaiController::class, 'searchById']);

//route pegawai
Route::get('/pegawai', [PegawaiController::class, 'index']);           
Route::post('/pegawai', [PegawaiController::class, 'store']);         
Route::get('/pegawai/{id}', [PegawaiController::class, 'show']);       
Route::put('/pegawai/{id}', [PegawaiController::class, 'update']);     
Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy']);  
Route::get('/pegawai/search/{name}', [PegawaiController::class, 'searchByName']);

//route perawatan
Route::get('/perawatan', [PerawatanController::class, 'index']);
Route::post('/perawatan', [PerawatanController::class, 'store']);
Route::get('/perawatan/{id}', [PerawatanController::class, 'show']);
Route::post('/perawatan/{id}', [PerawatanController::class, 'update']);
Route::delete('/perawatan/{id}', [PerawatanController::class, 'destroy']);
Route::get('/perawatan/search/{name}', [PerawatanController::class, 'searchByName']);

//route produk
Route::get('/produk', [ProdukController::class, 'index']);
Route::post('/produk', [ProdukController::class, 'store']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);
Route::post('/produk/{id}', [ProdukController::class, 'update']);
Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
Route::get('/produk/search/{name}', [ProdukController::class, 'searchByName']);

//route ruangan
Route::get('/ruangan', [RuanganController::class, 'index']);
Route::post('/ruangan', [RuanganController::class, 'store']);
Route::get('/ruangan/{id}', [RuanganController::class, 'show']);
Route::put('/ruangan/{id}', [RuanganController::class, 'update']);
Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy']);
Route::get('/ruangan/search/{no_ruangan}', [RuanganController::class, 'searchByNoRuangan']);


//route promo
Route::get('/promo', [PromoController::class, 'index']);
Route::post('/promo', [PromoController::class, 'store']);
Route::get('/promo/{id}', [PromoController::class, 'show']);
Route::put('/promo/{id}', [PromoController::class, 'update']);
Route::delete('/promo/{id}', [PromoController::class, 'destroy']);
Route::get('/promo/search/{name}', [PromoController::class, 'searchByName']);

Route::get('/laporan-customer', [LaporanController::class, 'tampilLaporanCustomer']);
Route::get('/laporan-pendapatan', [LaporanController::class, 'tampilLaporanPendapatan']);
//route promo
Route::get('/transaksi', [TransaksiController::class, 'index']);
Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
Route::get('/transaksi/showPegawai/{id}', [TransaksiController::class, 'showPegawai']);
Route::get('/transaksi/showCustomer/{id}', [TransaksiController::class, 'showCustomer']);
Route::post('/transaksi', [TransaksiController::class, 'store']);
Route::get('/transaksiBelumBayar', [TransaksiController::class, 'transaksiBelumBayar']);
Route::get('/transaksi/showAntrianDokter/{id}', [TransaksiController::class, 'showAntrianPerDokter']);
Route::get('/transaksi/showRiwayatCustomer/{id}', [TransaksiController::class, 'showRiwayatCustomer']);
Route::patch('/transaksi/inputDataPemeriksaan/{id}', [TransaksiController::class, 'inputDataPemeriksaan']);
Route::put('/transaksi/bayarTransaksi/{id}', [TransaksiController::class, 'bayarTransaksi']);
Route::post('/transaksi/inputProduk', [TransaksiController::class, 'inputProduk']);
Route::post('/transaksi/inputPerawatan', [TransaksiController::class, 'inputPerawatan']);
Route::post('/promo', [TransaksiController::class, 'store']);
Route::get('/promo/{id}', [TransaksiController::class, 'show']);
Route::put('/promo/{id}', [TransaksiController::class, 'update']);
Route::delete('/promo/{id}', [TransaksiController::class, 'destroy']);
Route::get('/promo/search/{name}', [TransaksiController::class, 'searchByName']);





//route ruangan
Route::get('/ruangan', [RuanganController::class, 'index']);
Route::post('/ruangan', [RuanganController::class, 'store']);
Route::get('/ruangan/{id}', [RuanganController::class, 'show']);
Route::put('/ruangan/{id}', [RuanganController::class, 'update']);
Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy']);
Route::get('/ruangan/search/{no_ruangan}', [RuanganController::class, 'searchByNoRuang']);


//route perawatan
Route::get('/perawatan', [PerawatanController::class, 'index']);
Route::post('/perawatan', [PerawatanController::class, 'store']);
Route::get('/perawatan/{id}', [PerawatanController::class, 'show']);
Route::put('/perawatan/{id}', [PerawatanController::class, 'update']);
Route::delete('/perawatan/{id}', [PerawatanController::class, 'destroy']);
Route::get('/perawatan/search/{name}', [PerawatanController::class, 'searchByName']);

//route customer
Route::get('/searchByName/{name}', [CustomerController::class, 'searchByName']);

//route laporan
Route::get('/perawatanTerlaris', [LaporanController::class, 'getPerawatanTerlaris']);
Route::get('/customerPerDokter/{bulan}/{tahun}', [LaporanController::class, 'getJumlahCustomerPerDokter']);
Route::get('/monthly-sales-report', [PDFController::class, 'showMonthlySalesReport']);
//Route::get('/monthly-sales-product-report', [PDFProdukController::class, 'showMonthlyProductSalesReport']);
//Route::get('/laporan-bahan-baku/{startDate}/{endDate}', [PDFController::class, 'showUsageMaterialReport']);
//Route::get('/laporan-produk/{startDate}/{endDate}', [PDFProdukController::class, 'showUsageProductReport']);
Route::post('/top10', [PDFProdukController::class, 'top10LatestSuccessfulTransactions']);
Route::get('/top10', [PDFProdukController::class, 'top10LatestSuccessfulTransactions']);
Route::get('/produkTerlaris', [PDFProdukController::class, 'getProdukTerlaris']);
Route::get('/latestSuccessfulPaymentsWithProducts', [PDFProdukController::class, 'top10LatestSuccessfulTransactions']);

