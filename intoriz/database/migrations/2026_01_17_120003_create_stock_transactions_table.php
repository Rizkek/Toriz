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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment']); // in=purchase, out=sale, adjustment=manual
            $table->integer('quantity'); // Positive for in, negative for out
            $table->integer('before_qty');
            $table->integer('after_qty');
            $table->string('reference_type')->nullable(); // PurchaseOrder, StockOut, Manual
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of related record
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Who made the transaction
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();

            $table->index(['product_id', 'transaction_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
