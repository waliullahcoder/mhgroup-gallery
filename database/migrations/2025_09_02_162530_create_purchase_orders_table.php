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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();

            $table->date('order_date');
            $table->date('expected_date')->nullable();

            $table->decimal('total_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('discount_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('tax_amount', 12, 2)->unsigned()->default(0);
            $table->decimal('grand_total', 12, 2)->unsigned()->default(0);

            $table->enum('status', ['draft', 'pending', 'approved', 'partially_received', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
