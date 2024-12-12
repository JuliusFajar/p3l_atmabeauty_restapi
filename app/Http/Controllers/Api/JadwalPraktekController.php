<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Jadwal;
use App\Models\Jadwal_Praktek;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JadwalPraktekController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $jadwal_Praktek = Jadwal_Praktek::all();

        if (count($jadwal_Praktek) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $jadwal_Praktek
            ], 200);
        }

        return response([
            'message' => 'empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $storeData = $request->all();

        $validate = Validator::make($storeData, [
            'id_pegawai'  => 'required',
            'id_jadwal'  => 'required',
            'shift'  => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $jadwal_Praktek = Jadwal_Praktek::create($storeData);
        return response([
            'message' => 'Add Jadwal Praktek Success',
            'data' => $jadwal_Praktek
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $jadwal_Praktek = Jadwal_Praktek::find($id);

        if(!is_null($jadwal_Praktek)){
            return response([
                'message' => 'Jadwal Praktek Found, it is. ' . $jadwal_Praktek->jadwal_praktek,
                'data' => $jadwal_Praktek
            ], 200);
        }

        return response([
            'message' => 'Jadwal Praktek Not Found',
            'data' => null
        ], 400);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $jadwal_Praktek = Jadwal_Praktek::find($id);
        if (is_null($jadwal_Praktek)) {
            return response([
                'message' => 'Jadwal Praktek Not Found',
                'data' => null
            ], 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_pegawai'  => 'required',
            'id_jadwal'  => 'required',
            'shift'  => 'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);
        $jadwal_Praktek->stock = $updateData['jadwal_Praktek'];

        if ($jadwal_Praktek->save()) {
            return response([
                'message' => 'Update Jadwal Praktek Success',
                'data' => $jadwal_Praktek
            ], 200);
        }
        return response([
            'message' => 'Update jadwal praktek Fail',
            'data' => null
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $jadwal_Praktek = Jadwal_Praktek::find($id);
        if (is_null($jadwal_Praktek)) {
            return response([
                'message' => 'Jadwal Praktek Not Found',
                'data' => null
            ], 400);
        }
        if ($jadwal_Praktek->delete()) {
            return response([
                'message' => 'Delete Jadwal Praktek Success',
                'data' => $jadwal_Praktek
            ], 200);
        }
        return response([
            'message' => 'Delete Jadwal Praktek Failed',
            'data' => null
        ], 400);
    }
}
