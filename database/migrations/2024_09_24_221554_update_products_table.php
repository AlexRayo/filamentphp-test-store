<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Cambiar el nombre del campo 'price' a 'buy_price'
            $table->renameColumn('price', 'buy_price');

            // Agregar nuevos campos
            $table->decimal('sell_price', 10, 2)->after('buy_price');
            $table->decimal('iva', 5, 2)->after('sell_price')->default(0);
            $table->decimal('discount', 5, 2)->after('iva')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
