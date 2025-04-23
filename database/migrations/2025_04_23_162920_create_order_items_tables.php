<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Verificamos si las columnas ya existen
            if (!Schema::hasColumn('order_items', 'product_name')) {
                // Modificar la restricción de clave foránea para product_id
                $table->dropForeign(['product_id']);
                $table->foreign('product_id')
                      ->references('id')
                      ->on('products')
                      ->onDelete('set null')
                      ->change();
                
                // Hacer que product_id sea nullable
                $table->foreignId('product_id')
                      ->nullable()
                      ->change();
                
                // Añadir nuevas columnas
                $table->string('product_name')->after('product_id');
                $table->string('product_sku')->nullable()->after('product_name');
            }
        });
    }

    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_name')) {
                // Eliminar las columnas añadidas
                $table->dropColumn(['product_name', 'product_sku']);
                
                // Restaurar la restricción original de clave foránea
                $table->dropForeign(['product_id']);
                $table->foreignId('product_id')
                      ->nullable(false)
                      ->constrained()
                      ->onDelete('cascade')
                      ->change();
            }
        });
    }
};