<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Verificar si estas columnas no existen antes de agregarlas
            if (!Schema::hasColumn('orders', 'payment_method_id')) {
                $table->foreignId('payment_method_id')->nullable()->after('order_status_id')->constrained()->onDelete('set null');
            }
            
            // Primero agregamos delivery_method_id si no existe
            if (!Schema::hasColumn('orders', 'delivery_method_id')) {
                $table->foreignId('delivery_method_id')->nullable()->constrained()->onDelete('set null');
            }
            
            // Luego agregamos address_id después de delivery_method_id
            if (!Schema::hasColumn('orders', 'address_id')) {
                $table->foreignId('address_id')->nullable()->after('delivery_method_id')->constrained()->onDelete('set null');
            }
            
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('total')->default(0);
            }
            
            if (!Schema::hasColumn('orders', 'tax')) {
                $table->decimal('tax', 10, 2)->default(0)->after('subtotal');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('tax');
            }
            
            if (!Schema::hasColumn('orders', 'discount')) {
                $table->decimal('discount', 10, 2)->default(0)->after('shipping_cost');
            }
            
            if (!Schema::hasColumn('orders', 'currency')) {
                $table->string('currency')->default('USD')->after('total');
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('pending')->after('currency');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status')->default('pending')->after('payment_status');
            }
            
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('shipping_status');
            }
            
            if (!Schema::hasColumn('orders', 'guest_email')) {
                $table->string('guest_email')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('orders', 'guest_name')) {
                $table->string('guest_name')->nullable()->after('guest_email');
            }
            
            if (!Schema::hasColumn('orders', 'is_guest_checkout')) {
                $table->boolean('is_guest_checkout')->default(false)->after('guest_name');
            }
            
            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('is_guest_checkout');
            }
            
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('paid_at');
            }
            
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
            
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Aquí podrías revertir los cambios si es necesario
        });
    }
};