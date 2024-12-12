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
        Schema::table('promo', function (Blueprint $table) {
            $table->id('id_promo');
            $table->string('nama_promo');
            $table->text('keterangan_promo')->nullable();
            $table->decimal('potongan_promo', 8, 2);
            $table->integer('tambah_poin')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo', function (Blueprint $table) {
            //
        });
    }
};
