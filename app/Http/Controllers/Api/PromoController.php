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
                'message' => 'Berhasil Mendapatkan Data Promo',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Data Promo Kosong',
            'data' => null
        ], 400);
    }

    public function promoYangTersedia()
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
        'kode_promo' => 'required',
        'jenis_promo' => 'required',
        'keterangan' => 'required'  
    ]);

    if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $promo = Promo::create($storeData);
        return response([
            'message' => 'Berhasil Menambahkan Data Promo',
            'data' => $promo
        ], 200);
}


    public function show(string $id)
    {
        $promo = Promo::find($id);

        if ($promo) {
            return response([
                'message' => 'Data Promo Ditemukan',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Data Promo Tidak Ditemukan',
            'data' => null
        ], 400);
    }

    public function update(Request $request, string $id)
    {
        $promo = Promo::find($id);
        if (is_null($promo)) {
            return response([
                'message' => 'Data Promo Tidak Ditemukan',
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


        if ($promo->save()) {
            return response([
                'message' => 'Data Promo Berhasil Diperbaharui',
                'data' => $promo
            ], 200);
        }
        return response([
            'message' => 'Data Promo Gagal Diperbaharui',
            'data' => null
        ], 400);
    }

    public function destroy(string $id)
    {
        $promo = Promo::find($id);
        if (is_null($promo)) {
            return response([
                'message' => 'Data Promo Tidak Ditemukan',
                'data' => null
            ], 400);
        }

        if ($promo->delete()) {
            return response([
                'message' => 'Data Promo Berhasil Dihapus',
                'data' => $promo
            ], 200);
        }

        return response([
            'message' => 'Data Promo Gagal Dihapus',
            'data' => null
        ], 400);
    }

}
