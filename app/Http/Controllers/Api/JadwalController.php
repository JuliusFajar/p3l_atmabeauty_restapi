<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Jadwal;

class JadwalController extends Controller
{
    public function index()
    {
        try{
            $jadwal = Jadwal::all();
            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function show($id)
    {
        try{
            $jadwal = User::where('id', '=', $id)->select('id_jadwal', 'hari', )->first();

            if(!$jadwal) throw new \Exception("Jadwal tidak ditemukan");

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try{
            $jadwal = Jadwal::create($request->all());
            return response()->json([
                "status" => true,
                "message" => 'Berhasil menambah jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $jadwal = Jadwal::find($id);

            if(!$jadwal) throw new \Exception("jadwal tidak ditemukan");

            $jadwal->update($request->all());

            return response()->json([
                "status" => true,
                "message" => 'Berhasil mengubah jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function destroy($id)
    {
        try{
            $jadwal = Jadwal::find($id);

            if(!$jadwal) throw new \Exception("Jadwal tidak ditemukan");

            $jadwal->delete();

            return response()->json([
                "status" => true,
                "message" => 'Berhasil menghapus jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function searchById($id)
    {
        try{
            $jadwal = User::where('id', '=', $id)->select('id_jadwal', 'hari',)->first();

            if(!$jadwal) throw new \Exception("Id Jadwal tidak ditemukan");

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data jadwal',
                "data" => $jadwal
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }
}
