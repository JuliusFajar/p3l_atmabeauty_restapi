<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $customer = Customer::all();
            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $customer = User::where('id', '=', $id)->select('id_customer', 'username', 'tanggal_lahir', 'jenis_kelamin', 'alamat_lengkap', 'nomor_telepon', 'alamat_email', 'alergi_obat', 'poin', 'tanggal_registrasi', 'password', 'foto_profil', )->first();

            if (!$customer)
                throw new \Exception("Data Customer tidak ditemukan");

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function store(Request $request)
{
    // Validate required fields
    $validator = Validator::make($request->all(), [
        'nama_customer' => 'required|string|max:255',
        'username' => 'required|string|max:25',
        'tanggal_lahir' => 'required|date',
        'jenis_kelamin' => 'required|string|max:255',
        'alamat_customer' => 'required|string|max:255',
        'nomor_telepon' => 'required|string|max:255',
        'email_customer' => 'required|email|max:255',
        'alergi_obat' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:6', 
        'profile_customer' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            "status" => false,
            "message" => "Validation error",
            "errors" => $validator->errors()
        ], 422);
    }

    try {
        // Generate the customer ID
        $currentDate = Carbon::now();
        $monthRegistration = $currentDate->format('m');
        $yearRegistration = $currentDate->format('y');
        $birthDate = Carbon::parse($request->tanggal_lahir);
        $dayOfBirth = $birthDate->format('d');
        $monthOfBirth = $birthDate->format('m');
        $yearOfBirth = $birthDate->format('Y');
        $lastCustomer = Customer::orderBy('id_customer', 'desc')->first();
        $sequenceNumber = $lastCustomer ? $lastCustomer->id_customer + 1 : 1;
        $customerId = "{$monthRegistration}{$yearRegistration} {$dayOfBirth}{$monthOfBirth} {$yearOfBirth} {$sequenceNumber}";

        // Set the password to `tanggal_lahir` if not provided
        $password = $request->password ? $request->password : $request->tanggal_lahir;

        // Create the customer with validated data
        $customer = Customer::create([
            'nomor_customer' => $customerId,
            'nama_customer' => $request->nama_customer,
            'username' => $request->username,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat_customer' => $request->alamat_customer,
            'nomor_telepon' => $request->nomor_telepon,
            'email_customer' => $request->email_customer,
            'alergi_obat' => $request->alergi_obat,
            'poin_customer' => $request->poin_customer ?? 0,
            'tanggal_registrasi' => $currentDate->toDateString(),
            'password' => $password, // Store the password without hashing
            'profile_customer' => $request->profile_customer,
        ]);

        return response()->json([
            "status" => true,
            "message" => 'Berhasil menambah customer',
            "data" => $customer
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            "status" => false,
            "message" => $e->getMessage(),
            "data" => []
        ], 400);
    }
}


    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::find($id);

            if (!$customer)
                throw new \Exception("Data Customer tidak ditemukan");

            $customer->update($request->all());

            return response()->json([
                "status" => true,
                "message" => 'Berhasil mengubah data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);

            if (!$customer)
                throw new \Exception("Data Customer tidak ditemukan");

            $customer->delete();

            return response()->json([
                "status" => true,
                "message" => 'Berhasil menghapus data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function searchByEmail($email)
    {
        try {
            $customer = User::where('email', '=', $email)->select('id_customer', 'username', 'tanggal_lahir', 'jenis_kelamin', 'alamat_lengkap', 'nomor_telepon', 'alamat_email', 'alergi_obat', 'poin', 'tanggal_registrasi', 'password', 'foto_profil', )->first();

            if (!$customer)
                throw new \Exception("Data Customer tidak ditemukan");

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function searchByName($name)
    {
        try {
            $customer = Customer::where('nama_customer', 'LIKE', '%' . $name . '%')->get();

            if ($customer->isEmpty()) {
                throw new \Exception("Customer dengan nama tersebut tidak ditemukan");
            }

            return response()->json([
                "status" => true,
                "message" => 'Berhasil ambil data customer',
                "data" => $customer
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
                "data" => []
            ], 400);
        }
    }

    public function generateNoCustomer(Request $request)
{
    try {
        // Validate the request (if needed, you can add specific validation rules)
        $validator = Validator::make($request->all(), [
            'tanggal_lahir' => 'required|date', // Example: if you need to input date of birth
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ], 422);
        }

        // Get the current date for registration
        $currentDate = Carbon::now();
        $monthRegistration = $currentDate->format('m');
        $yearRegistration = $currentDate->format('y');

        // Parse the birth date from the request
        $birthDate = Carbon::parse($request->tanggal_lahir);
        $dayOfBirth = $birthDate->format('d');
        $monthOfBirth = $birthDate->format('m');
        $yearOfBirth = $birthDate->format('Y');

        // Get the last customer to generate the sequence number
        $lastCustomer = Customer::orderBy('id_customer', 'desc')->first();
        $sequenceNumber = $lastCustomer ? $lastCustomer->id_customer + 1 : 1;

        // Generate the customer ID
        $customerId = "{$monthRegistration}{$yearRegistration} {$dayOfBirth}{$monthOfBirth} {$yearOfBirth} {$sequenceNumber}";

        return response()->json([
            "status" => true,
            "message" => 'Berhasil menghasilkan nomor customer',
            "data" => [
                "nomor_customer" => $customerId
            ]
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
