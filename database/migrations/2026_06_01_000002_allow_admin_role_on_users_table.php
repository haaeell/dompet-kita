<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('partner')->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'admin')->update(['role' => 'partner']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'partner'])->default('partner')->change();
        });
    }
};
