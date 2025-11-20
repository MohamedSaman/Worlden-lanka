<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE payments 
            MODIFY payment_method ENUM(
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'cheque',
                'credit',
                'cash and cheque'
            ) DEFAULT 'cash'
        ");
    }

    public function down(): void
    {
        // Remove "cash and cheque" (restore original enum)
        DB::statement("
            ALTER TABLE payments 
            MODIFY payment_method ENUM(
                'cash',
                'credit_card',
                'debit_card',
                'bank_transfer',
                'cheque',
                'credit'
            ) DEFAULT 'cash'
        ");
    }
};
