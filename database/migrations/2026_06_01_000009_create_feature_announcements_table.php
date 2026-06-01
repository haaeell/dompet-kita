<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('feature');
            $table->string('version')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feature_announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['feature_announcement_id', 'user_id'], 'announcement_user_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_announcement_reads');
        Schema::dropIfExists('feature_announcements');
    }
};
