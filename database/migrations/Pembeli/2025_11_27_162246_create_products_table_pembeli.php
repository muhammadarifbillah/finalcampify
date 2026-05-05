<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('category');
    $table->text('description');
$table->integer('buy_price')->nullable();
$table->integer('rent_price')->nullable();
    $table->float('rating')->default(0);
    $table->integer('reviews_count')->default(0);
    $table->string('image');
    $table->integer('stock')->default(0);
    $table->timestamps();
});
    }
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
