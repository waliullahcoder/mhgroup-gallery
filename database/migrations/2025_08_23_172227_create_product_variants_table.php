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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('variant')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('purchase_price', 16, 2)->default(0.00);
            $table->decimal('regular_price', 16, 2)->default(0.00);
            $table->decimal('sale_price', 16, 2)->default(0.00);
            $table->decimal('discount', 16, 2)->default(0.00);
            $table->string('discount_type', 10)->default('amount');
            $table->string('image')->nullable();
            $table->integer('stock')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
