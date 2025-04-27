<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->softDeletes(); // Añade la columna 'deleted_at' nullable
        });
    }

    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Permite revertir la migración
        });
    }
};
