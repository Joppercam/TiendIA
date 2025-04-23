<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Primero verificamos si estas columnas ya existen para evitar errores
            if (!Schema::hasColumn('orders', 'order_status_id')) {
                // Eliminar la columna status actual
                $table->dropColumn('status');
                
                // Añadir las nuevas columnas
                $table->foreignId('order_status_id')->after('user_id')->constrained();
                $table->foreignId('payment_method_id')->nullable()->after('order_status_id')->constrained()->onDelete('set null');
                $table->foreignId('delivery_method_id')->nullable()->after('payment_method_id')->constrained()->onDelete('set null');
                $table->foreignId('address_id')->nullable()->after('delivery_method_id')->constrained()->onDelete('set null');
                
                $table->decimal('subtotal', 10, 2)->after('total');
                $table->decimal('tax', 10, 2)->default(0)->after('subtotal');
                $table->decimal('shipping_cost', 10, 2)->default(0)->after('tax');
                $table->decimal('discount', 10, 2)->default(0)->after('shipping_cost');
                
                $table->string('currency')->default('USD')->after('total');
                $table->string('payment_status')->default('pending')->after('currency');
                $table->string('shipping_status')->default('pending')->after('payment_status');
                
                $table->text('notes')->nullable()->after('shipping_status');
                $table->string('guest_email')->nullable()->after('notes');
                $table->string('guest_name')->nullable()->after('guest_email');
                $table->boolean('is_guest_checkout')->default(false)->after('guest_name');
                
                $table->timestamp('paid_at')->nullable()->after('is_guest_checkout');
                $table->timestamp('shipped_at')->nullable()->after('paid_at');
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
                $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Si necesitamos revertir los cambios
            if (Schema::hasColumn('orders', 'order_status_id')) {
                $table->dropForeign(['order_status_id']);
                $table->dropForeign(['payment_method_id']);
                $table->dropForeign(['delivery_method_id']);
                $table->dropForeign(['address_id']);
                
                $table->dropColumn([
                    'order_status_id',
                    'payment_method_id',
                    'delivery_method_id',
                    'address_id',
                    'subtotal',
                    'tax',
                    'shipping_cost',
                    'discount',
                    'currency',
                    'payment_status',
                    'shipping_status',
                    'notes',
                    'guest_email',
                    'guest_name',
                    'is_guest_checkout',
                    'paid_at',
                    'shipped_at',
                    'delivered_at',
                    'cancelled_at'
                ]);
                
                // Restaurar la columna status original
                $table->string('status')->after('total');
            }
        });
    }
};