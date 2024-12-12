<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Validator;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::all();

        if ($ruangan->isNotEmpty()) {
            return response([
                'message' => 'Berhasil Mendapatkan Data Ruangan',
                'data' => $ruangan
            ], 200);
        }

        return response([
            'message' => 'Data Ruangan Kosong',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'no_ruangan' => 'required' 
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $ruangan = Ruangan::create($storeData);
        return response([
            'message' => 'Data Ruangan Berhasil Ditambahkan',
            'data' => $ruangan
        ], 200);
    }

    public function show(string $id)
    {
        $ruangan = Ruangan::find($id);

        if ($ruangan) {
            return response([
                'message' => 'Data Ruangan Ditemukan',
                'data' => $ruangan
            ], 200);
        }

        return response([
            'message' => 'Data Ruangan Tidak Ditemukan',
            'data' => null
        ], 400);
    }

    public function update(Request $request, string $id)
    {
        $ruangan = Ruangan::find($id);
        if (is_null($ruangan)) {
            return response([
                'message' => 'Data Ruangan Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'no_ruangan' => 'required'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $ruangan->update($updateData);

        if ($ruangan->save()) {
            return response([
                'message' => 'Data Ruangan Berhasil Diperbaharui',
                'data' => $ruangan
            ], 200);
        }

        return response([
            'message' => 'Data Ruangan Gagal Diperbaharui',
            'data' => null
        ], 400);
    }

    public function destroy(string $id)
    {
        $ruangan = Ruangan::find($id);
        if (is_null($ruangan)) {
            return response([
                'message' => 'Data Ruangan Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        if ($ruangan->delete()) {
            return response([
                'message' => 'Data Ruangan Berhasil Dihapus',
                'data' => $ruangan
            ], 200);
        }

        return response([
            'message' => 'Data Ruangan Gagal Dihapus',
            'data' => null
        ], 400);
    }


    public function searchByNoRuangan($no_ruangan)
{
    try {
        $ruangan = Ruangan::where('no_ruangan', 'LIKE', '%' . $no_ruangan . '%')->get();

        if ($ruangan->isEmpty()) {
            return response()->json([
                "status" => false,
                "message" => "Ruangan dengan nomor tersebut tidak ditemukan",
                "data" => []
            ], 404);
        }

        return response()->json([
            "status" => true,
            "message" => 'Berhasil mengambil data ruangan',
            "data" => $ruangan
        ], 200);
    } 
    catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => "Terjadi kesalahan: " . $e->getMessage(),
            "data" => []
        ], 500);
    }
}
}
