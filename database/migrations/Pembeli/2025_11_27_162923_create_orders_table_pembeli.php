<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('total');
            $table->string('metode_pembayaran'); // qris, bank, cod
            $table->string('status')->default('menunggu'); // menunggu, diproses, dikirim, selesai, dibatalkan
            $table->string('kurir')->nullable();
            $table->string('no_resi')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
