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
        if (!Schema::hasColumn('sales_items', 'product_name')) {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->string('product_name')->after('product_id')->nullable();
            });
        }

        Schema::table('sales_items', function (Blueprint $table) {
            // Make product_id nullable if it's not already
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            // Re-setup foreign key with nullOnDelete
            // We'll wrap this in a try-catch or check if possible to avoid errors if FK doesn't exist
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore
            }

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
            try {
                $table->dropForeign(['product_id']);
            } catch (\Exception $e) {}

            $table->foreign('product_id')
                ->references('id')
                ->on('product_details')
                ->onDelete('cascade');
                
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            
            if (Schema::hasColumn('sales_items', 'product_name')) {
                $table->dropColumn('product_name');
            }
        });
    }
};
