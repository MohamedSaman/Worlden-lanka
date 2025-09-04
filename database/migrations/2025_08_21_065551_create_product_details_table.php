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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->nullable()->change();
            $table->string('product_name');
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('cascade');
            $table->decimal('supplier_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->integer('stock_quantity');
            $table->integer('damage_quantity');
            $table->json('customer_field')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
