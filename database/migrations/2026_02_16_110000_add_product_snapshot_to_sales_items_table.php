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
        Schema::table('sales_items', function (Blueprint $table) {
            $table->string('product_name')->after('product_id')->nullable();
            
            // Drop existing foreign key and add new one with nullOnDelete
            $table->dropForeign(['product_id']);
            
            // Make product_id nullable and set foreign key with nullOnDelete
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            $table->foreign('product_id')
                ->references('id')
                ->on('product_details')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            
            $table->foreign('product_id')
                ->references('id')
                ->on('product_details')
                ->onDelete('cascade');
                
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->dropColumn('product_name');
        });
    }
};
