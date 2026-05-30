<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('couple_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 10, 2)->unsigned()->nullable();
            $table->string('label', 80)->default('Lagi di sini');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['couple_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_locations');
    }
};
