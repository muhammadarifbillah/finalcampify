<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->cascadeOnDelete();
            $table->string('resi_return')->nullable();
            $table->string('bukti_denda')->nullable();
            $table->string('kondisi_barang')->default('baik');
            $table->integer('denda')->default(0);
            $table->dateTime('tanggal_pengembalian')->nullable();
            $table->timestamps();

            $table->unique('rental_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};

