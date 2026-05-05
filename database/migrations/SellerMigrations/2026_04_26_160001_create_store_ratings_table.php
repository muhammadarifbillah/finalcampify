<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('store_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id'); // user_id seller
            $table->unsignedBigInteger('user_id'); // user yang memberi rating
            $table->integer('rating'); // 1-5
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->foreign('store_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            $table->unique(['store_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('store_ratings');
    }
};