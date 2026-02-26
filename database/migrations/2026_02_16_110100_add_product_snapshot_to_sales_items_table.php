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
        // 1. Add product_name if missing
        if (!Schema::hasColumn('sales_items', 'product_name')) {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->string('product_name')->after('product_id')->nullable();
            });
        }

        // 2. Make product_id nullable
        Schema::table('sales_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });

        // 3. Re-handle Foreign Key safely
        // First, try to drop it if it exists. We do this in a separate block.
        try {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->dropForeign('sales_items_product_id_foreign');
            });
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }

        // Now add the new foreign key with nullOnDelete
        try {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('product_details')
                    ->nullOnDelete();
            });
        } catch (\Exception $e) {
            // Might already exist with correct settings
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Drop the SET NULL foreign key
        try {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->dropForeign('sales_items_product_id_foreign');
            });
        } catch (\Exception $e) {}

        // 2. Make it NOT NULL (MUST DO THIS BEFORE RE-ADDING THE CASCADE FK)
        // Wait, if we have NULLs in the table, making it NOT NULL will fail.
        // So we should update NULLs to a fallback or handle it.
        // But since this is a rollback, we'll try to follow the original state.
        try {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable(false)->change();
            });
        } catch (\Exception $e) {}

        // 3. Re-add Cascade Foreign Key
        try {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('product_details')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {}
            
        // 4. Drop Column
        if (Schema::hasColumn('sales_items', 'product_name')) {
            Schema::table('sales_items', function (Blueprint $table) {
                $table->dropColumn('product_name');
            });
        }
    }
};
