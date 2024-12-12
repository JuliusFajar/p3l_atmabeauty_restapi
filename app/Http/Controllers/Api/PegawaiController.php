<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all();

        if (count($pegawai) > 0) {
            return response([
                'message' => 'Berhasil Mendapatkan Data Pegawai',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Data Pegawai Kosong',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_ruangan' => 'nullable',
            'jabatan_pegawai' => 'required',
            'nama_pegawai' => 'required',
            'alamat_pegawai' => 'required',
            'nomor_telepon' => 'required',
            'status_pegawai' => 'nullable',
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $pegawai = Pegawai::create($storeData);
        return response([
            'message' => 'Berhasil Menambahkan Data Pegawai',
            'data' => $pegawai
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $pegawai = Pegawai::find($id);
        if (is_null($pegawai)) {
            return response([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_ruangan' => 'nullable',
            'jabatan_pegawai' => 'required',
            'nama_pegawai' => 'required',
            'alamat_pegawai' => 'required',
            'nomor_telepon' => 'required',
            'status_pegawai' => 'nullable',
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $pegawai->id_ruangan = $updateData['id_ruangan'];
        $pegawai->jabatan_pegawai = $updateData['jabatan_pegawai'];
        $pegawai->nama_pegawai = $updateData['nama_pegawai'];
        $pegawai->alamat_pegawai = $updateData['alamat_pegawai'];
        $pegawai->nomor_telepon = $updateData['nomor_telepon'];
        $pegawai->status_pegawai = $updateData['status_pegawai'];
        $pegawai->username = $updateData['username'];
        $pegawai->password =$updateData['password'];

        if ($pegawai->save()) {
            return response([
                'message' => 'Update Pegawai Success',
                'data' => $pegawai
            ], 200);
        }
        return response([
            'message' => 'Update Pegawai Fail',
            'data' => null
        ], 400);
    }

    public function destroy(string $id)
    {
        $pegawai = Pegawai::find($id);
        if (is_null($pegawai)) {
            return response([
                'message' => 'Data Pegawai Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        if ($pegawai->delete()) {
            return response([
                'message' => 'Data Pegawai Berhasil Dihapus',
                'data' => $pegawai
            ], 200);
        }
        
        return response([
            'message' => 'Data Pegawai Gagal Dihapus',
            'data' => null
        ], 400);
    }

    public function show(string $id)
    {
        $pegawai = Pegawai::find($id);

        if (!is_null($pegawai)) {
            return response([
                'message' => 'Pegawai Found, it is. ' . $pegawai->nama_pegawai,
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Pegawai Not Found',
            'data' => null
        ], 400);
    }

    public function searchByName($name)
    {
        try {
            $pegawai = Pegawai::where('nama_pegawai', 'LIKE', '%' . $name . '%')->get();

            if ($pegawai->isEmpty()) {
                throw new \Exception("Pegawai dengan nama tersebut tidak ditemukan");
            }

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data pegawai',
                "data" => $pegawai
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function searchByJabatan($jabatan)
{
    try {
        // Use a stricter comparison for more specific searches
        $pegawai = Pegawai::where('jabatan_pegawai', $jabatan)->get();

        if ($pegawai->isEmpty()) {
            throw new \Exception("Pegawai dengan jabatan '$jabatan' tidak ditemukan");
        }

        return response()->json([
            "status" => true,
            "message" => 'Berhasil ambil data jabatan pegawai',
            "data" => $pegawai
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => $e->getMessage(),
            "data" => []
        ], 400);
    }
}

public function searchById($id)
{
    try {
        $pegawai = Pegawai::findOrFail($id);

        return response()->json([
            "status" => true,
            "message" => 'Berhasil menemukan pegawai',
            "data" => $pegawai
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => 'Pegawai tidak ditemukan',
            "data" => []
        ], 404);
    }
}

}
