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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_branch')->constrained('branches');
            $table->foreignId('id_product')->constrained('products');
            $table->enum('movement_type', ['in', 'out']);
            $table->integer('quantity');
            $table->string('reason', 150)->nullable();
            $table->timestamp('movement_date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
