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
        Schema::create('manual_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_type')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_type', ['full', 'partial'])->default('full');
            $table->enum('payment_status', ['paid', 'partial', 'pending'])->default('paid');
            $table->text('notes')->nullable();
            $table->text('delivery_note')->nullable();
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('manual_sales_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_sale_id')->constrained()->onDelete('cascade');
            $table->string('product_name');
            $table->string('product_code')->nullable();
            $table->string('category')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('quantity_type')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('manual_sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_sale_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['cash', 'cheque', 'bank_transfer', 'credit']);
            $table->string('payment_reference')->nullable();
            $table->string('bank_name')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_sale_payments');
        Schema::dropIfExists('manual_sales_items');
        Schema::dropIfExists('manual_sales');
    }
};
