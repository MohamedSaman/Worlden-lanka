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
        Schema::create('customer_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade')->nullable();
            $table->decimal('back_forward_amount', 10, 2)->default(0);
            $table->decimal('current_due_amount', 10, 2)->default(0);
            $table->decimal('paid_due', 10, 2)->default(0);
            $table->decimal('total_due', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_accounts');
    }
};
