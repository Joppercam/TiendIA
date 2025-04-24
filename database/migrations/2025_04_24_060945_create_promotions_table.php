<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('promotion_type'); // featured_product, bundle, buy_x_get_y, discount
            $table->json('rules')->nullable(); // Reglas específicas de la promoción en formato JSON
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // Para definir el orden de aplicación
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};