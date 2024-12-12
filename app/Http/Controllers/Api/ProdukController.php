<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    public function index()
    {
        $produk = Produk::all();

        if ($produk->isNotEmpty()) {
            return response([
                'message' => 'Berhasil Ambil Semua Data Produk',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Data Produk Kosong',
            'data' => null
        ], 400);
    }



    public function store(Request $request)
{
    $storeData = $request->all();
    $validate = Validator::make($storeData, [
        'nama_produk' => 'required',
        'keterangan_produk' => 'required',
        'stock_produk' => 'required|numeric|min:0',
        'harga_produk' => 'required|numeric|min:0',
        'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    ]);

    if ($validate->fails()) {
        return response(['message' => $validate->errors()], 400);
    }

    if ($request->hasFile('gambar_produk')) {
        $imageName = time() . '.' . $request->gambar_produk->extension();
        $request->gambar_produk->move(public_path('images/produk'), $imageName);
        $storeData['gambar_produk'] = $imageName;
    }

    $produk = Produk::create($storeData);
    return response([
        'message' => 'Berhasil Tambah Data Produk',
        'data' => $produk
    ], 200);
}




    public function show(string $id)
    {
        $produk = Produk::find($id);

        if ($produk) {
            return response([
                'message' => 'Data Produk Ditemukan',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Data Produk Tidak Ditemukan',
            'data' => null
        ], 400);
    }


    public function update(Request $request, string $id)
    {
        $produk = Produk::find($id);
        if (is_null($produk)) {
            return response([
                'message' => 'Data Produk Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $validate = Validator::make($request->all(), [
            'nama_produk' => 'required',
            'keterangan_produk' => 'required',
            'stock_produk' => 'required|numeric|min:0',
            'harga_produk' => 'required|numeric|min:0', 
            'gambar_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

            if ($validate->fails()) {
                return response(['message' => $validate->errors()], 400);
            }

            $updateData = $request->only(['nama_produk', 'keterangan_produk', 'stock_produk', 'harga_produk']);

            if ($request->hasFile('gambar_produk')) {

            if ($produk->gambar_produk) {
                $oldImagePath = public_path('images/produk/' . $produk->gambar_produk);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . '.' . $request->gambar_produk->extension();
            $request->gambar_produk->move(public_path('images/produk'), $imageName);
            $updateData['gambar_produk'] = $imageName;  
        }

        $produk->update($updateData);

        return response([
            'message' => 'Berhasil Memperbaharui Data Produk',
            'data' => $produk
        ], 200);
    }



    public function destroy(string $id)
    {
        $produk = Produk::find($id);
        if (is_null($produk)) {
            return response([
                'message' => 'Data Produk Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        if ($produk->delete()) {
            return response([
                'message' => 'Data Produk Berhasil Dihapus',
                'data' => $produk
            ], 200);
        }

        return response([
            'message' => 'Data Produk Gagal Dihapus',
            'data' => null
        ], 400);
    }



    public function searchByName($name)
    {
        try {
            $produk = Produk::where('nama_produk', 'LIKE', '%' . $name . '%')->get();

            if ($produk->isEmpty()) {
                return response()->json([
                    "status" => false,
                    "message" => 'Produk Dengan Nama ' . $name . ' Tidak Ditemukan',
                    "data" => []
                ], 404); 
            }
            return response()->json([
                "status" => true,
                "message" => 'Berhasil Ambil Data Produk Dengan Nama ' . $name,
                "data" => $produk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }
}
