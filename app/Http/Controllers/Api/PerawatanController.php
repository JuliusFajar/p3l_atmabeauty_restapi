<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PerawatanController extends Controller
{
    public function index()
    {
        $perawatan = Perawatan::all();

        if ($perawatan->isNotEmpty()) {
            return response([
                'message' => 'Berhasil Ambil Semua Data Perawatan',
                'data' => $perawatan
            ], 200);
        }

        return response([
            'message' => 'Data Perawatan Kosong',
            'data' => null
        ], 400);
    }



    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_perawatan' => 'required',
            'keterangan_perawatan' => 'required',
            'syarat_perawatan' => 'required',
            'harga_perawatan' => 'required|numeric|min:0', 
            'gambar_perawatan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        if ($request->hasFile('gambar_perawatan')) {
            $imageName = time() . '.' . $request->gambar_perawatan->extension();
            $request->gambar_perawatan->move(public_path('images/perawatan'), $imageName);
            $storeData['gambar_perawatan'] = $imageName;
        }

        $perawatan = Perawatan::create($storeData);
        return response([
            'message' => 'Berhasil Tambah Data Perawatan',
            'data' => $perawatan
        ], 200);
    }



    public function show(string $id)
    {
        $perawatan = Perawatan::find($id);

        if ($perawatan) {
            return response([
                'message' => 'Data Perawatan Ditemukan',
                'data' => $perawatan
            ], 200);
        }

        return response([
            'message' => 'Data Perawatan Tidak Ditemukan',
            'data' => null
        ], 400);
    }



    public function update(Request $request, string $id)
{
    $perawatan = Perawatan::find($id);
    if (is_null($perawatan)) {
        return response([
            'message' => 'Data Perawatan Tidak Ditemukan',
            'data' => null
        ], 404);
    }

    $validate = Validator::make($request->all(), [
        'nama_perawatan' => 'required',
        'keterangan_perawatan' => 'required',
        'syarat_perawatan' => 'required',
        'harga_perawatan' => 'required|numeric|min:0',
        'gambar_perawatan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' 
    ]);

    if ($validate->fails()) {
        return response(['message' => $validate->errors()], 400);
    }

    $updateData = $request->only(['nama_perawatan', 'keterangan_perawatan', 'syarat_perawatan', 'harga_perawatan']);

    if ($request->hasFile('gambar_perawatan')) {

        if ($perawatan->gambar_perawatan) {
            $oldImagePath = public_path('images/perawatan/' . $perawatan->gambar_perawatan);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $imageName = time() . '.' . $request->gambar_perawatan->extension();
        $request->gambar_perawatan->move(public_path('images/perawatan'), $imageName);
        $updateData['gambar_perawatan'] = $imageName;  // Tambahkan nama gambar baru ke data yang akan diupdate
    }

    $perawatan->update($updateData);

    return response([
        'message' => 'Berhasil Memperbaharui Data Perawatan',
        'data' => $perawatan
    ], 200);
}




    public function destroy(string $id)
    {
        $perawatan = Perawatan::find($id);
        if (is_null($perawatan)) {
            return response([
                'message' => 'Data Perawatan Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        if ($perawatan->delete()) {
            return response([
                'message' => 'Data Perawatan Berhasil Dihapus',
                'data' => $perawatan
            ], 200);
        }

        return response([
            'message' => 'Data Perawatan Gagal Dihapus',
            'data' => null
        ], 400);
    }



    public function searchByName($name)
    {
        try {
            $perawatan = Perawatan::where('nama_perawatan', 'LIKE', '%' . $name . '%')->get();

            if ($perawatan->isEmpty()) {
                return response()->json([
                    "status" => false,
                    "message" => 'Perawatan Dengan Nama ' . $name . ' Tidak Ditemukan',
                    "data" => []
                ], 404); 
            }
            return response()->json([
                "status" => true,
                "message" => 'Berhasil Ambil Data Perawatan Dengan Nama ' . $name,
                "data" => $perawatan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 500);
        }
    }
}
