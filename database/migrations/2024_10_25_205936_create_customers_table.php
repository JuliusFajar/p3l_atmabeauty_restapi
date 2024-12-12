<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id('id_customer');
            $table->string('nama_customer');
            $table->string('username')->unique();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat_customer');
            $table->string('nomor_telepon');
            $table->string('email_customer')->unique();
            $table->text('alergi_obat')->nullable();
            $table->integer('poin_customer')->default(0);
            $table->date('tanggal_registrasi');
            $table->string('password')->nullable();
            $table->string('profile_customer')->nullable();
            $table->timestamps(false, false); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
