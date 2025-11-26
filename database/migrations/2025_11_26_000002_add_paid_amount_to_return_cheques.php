<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('return_cheques', function (Blueprint $table) {
            if (!Schema::hasColumn('return_cheques', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('balance_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('return_cheques', function (Blueprint $table) {
            if (Schema::hasColumn('return_cheques', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
