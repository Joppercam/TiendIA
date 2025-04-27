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
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable'); // Para relación polimórfica con diferentes modelos
            $table->string('title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->json('structured_data')->nullable(); // Para datos estructurados (Rich Snippets)
            $table->text('additional_tags')->nullable(); // Para meta tags personalizados adicionales
            $table->string('canonical_url')->nullable(); // URL canónica para evitar contenido duplicado
            $table->boolean('no_index')->default(false); // Para indicar que no se debe indexar
            $table->boolean('no_follow')->default(false); // Para indicar que no se deben seguir los enlaces
            $table->timestamps();
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_url')->index();
            $table->string('target_url');
            $table->string('status_code')->default(301); // 301 (permanente) o 302 (temporal)
            $table->boolean('is_active')->default(true);
            $table->integer('hits')->default(0); // Contador de veces que se ha utilizado
            $table->string('last_accessed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sitemaps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'products', 'categories', 'pages', etc.
            $table->string('frequency')->default('weekly'); // Frecuencia de actualización
            $table->string('priority')->default('0.5'); // Prioridad para motores de búsqueda
            $table->string('filepath')->nullable(); // Ruta del archivo generado
            $table->timestamp('last_generated_at')->nullable();
            $table->json('settings')->nullable(); // Configuraciones adicionales
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('sitemaps');
    }
};