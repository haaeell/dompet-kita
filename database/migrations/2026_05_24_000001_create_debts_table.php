<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['hutang', 'piutang']);
            $table->decimal('amount', 15, 2);
            $table->string('counterparty');
            $table->string('purpose');
            $table->date('due_date');
            $table->foreignId('bank_id')->constrained('banks')->cascadeOnDelete();
            $table->foreignId('settlement_bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('initial_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
