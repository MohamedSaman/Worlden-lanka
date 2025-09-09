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
        Schema::table('cheques', function (Blueprint $table) {
            // Modify status enum to include 'cancel'
            $table->enum('status', ['complete', 'pending', 'return', 'cancel'])
                  ->default('pending')
                  ->change();

            // Add new column 'note'
            $table->text('note')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            // Revert status enum to previous version
            $table->enum('status', ['complete', 'pending', 'return'])
                  ->default('pending')
                  ->change();

            // Drop note column
            $table->dropColumn('note');
        });
    }
};
