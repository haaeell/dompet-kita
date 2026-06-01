<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedInteger('installment_count')->default(1)->after('amount');
            $table->decimal('installment_amount', 15, 2)->nullable()->after('installment_count');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('installment_amount');
            $table->date('last_payment_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn([
                'installment_count',
                'installment_amount',
                'paid_amount',
                'last_payment_at',
            ]);
        });
    }
};
