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
        Schema::table('perawatan', function (Blueprint $table) {
            Schema::create('perawatan', function (Blueprint $table) {
                $table->id('id_perawatan');
                $table->string('nama_perawatan');
                $table->text('keterangan_perawatan');
                $table->float('harga_perawatan');
                $table->string('gambar_perawatan');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perawatan', function (Blueprint $table) {
            //
        });
    }
};
