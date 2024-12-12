<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    public function index()
    {
        $promo = Promo::all();

        if ($promo->isNotEmpty()) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'nama_promo' => 'required',
            'keterangan_promo' => 'required',
            'potongan_promo' => 'required|numeric',
            'tambah_poin' => 'required|integer'
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $promo = Promo::create($storeData);
        return response([
            'message' => 'Add Promo Success',
            'data' => $promo
        ], 200);
    }

    public function show(string $id)
    {
        $promo = Promo::find($id);

        if ($promo) {
            return response([
                'message' => 'Promo Found',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Promo Not Found',
            'data' => null
        ], 400);
    }

    public function update(Request $request, string $id)
    {
        $promo = Promo::find($id);
        if (is_null($promo)) {
            return response([
                'message' => 'Promo Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'kode_promo' => 'required',
            'jenis_promo' => 'required',
            'keterangan' => 'required',
        ]);

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400);
        }

        $promo->update($updateData);
        $promo->kode_promo = $updateData['kode_promo'];
        $promo->jenis_promo = $updateData['jenis_promo'];
        $promo->keterangan = $updateData['keterangan'];


        if ($promo->save()) {
            return response([
                'message' => 'Update Produk Success',
                'data' => $promo
            ], 200);
        }
        return response([
            'message' => 'Update Produk Fail',
            'data' => null
        ], 400);
    }

    public function destroy(string $id)
    {
        $promo = Promo::find($id);
        if (is_null($promo)) {
            return response([
                'message' => 'Promo Not Found',
                'data' => null
            ], 400);
        }

        if ($promo->delete()) {
            return response([
                'message' => 'Delete Promo Success',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Delete Promo Failed',
            'data' => null
        ], 400);
    }
           

    public function searchByName($name)
    {
        try {
            $promo = Promo::where('nama_promo', 'LIKE', '%' . $name . '%')->get();

            if ($promo->isEmpty()) throw new \Exception("Promo dengan nama tersebut tidak ditemukan");

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data promo',
                "data" => $promo
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
