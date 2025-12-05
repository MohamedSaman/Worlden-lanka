<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quantity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Pieces", "Box", "Pack"
            $table->string('code')->unique(); // e.g., "pcs", "box", "pack"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quantity_types');
    }
};
