<?php

use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('couple_id')->constrained()->nullOnDelete();
            $table->index(['couple_id', 'user_id']);
        });

        Bank::query()
            ->whereNull('user_id')
            ->get(['id', 'couple_id', 'account_name'])
            ->each(function (Bank $bank) {
                $owner = User::query()
                    ->where('couple_id', $bank->couple_id)
                    ->where('name', $bank->account_name)
                    ->first();

                if ($owner) {
                    $bank->forceFill(['user_id' => $owner->id])->save();
                    return;
                }

                $fallbackOwner = User::query()
                    ->where('couple_id', $bank->couple_id)
                    ->orderByRaw("CASE WHEN role = 'owner' THEN 0 ELSE 1 END")
                    ->orderBy('id')
                    ->first();

                if ($fallbackOwner) {
                    $bank->forceFill(['user_id' => $fallbackOwner->id])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropIndex(['couple_id', 'user_id']);
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
