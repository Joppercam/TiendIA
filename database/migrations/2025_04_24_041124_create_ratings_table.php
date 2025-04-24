<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('review_id')->nullable()->constrained()->onDelete('set null');
            $table->tinyInteger('score')->unsigned(); // 1-5
            $table->timestamps();
            
            // Un usuario solo puede tener una calificación por producto
            $table->unique(['user_id', 'product_id']);
            
            // Índices para cálculos de promedios
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}