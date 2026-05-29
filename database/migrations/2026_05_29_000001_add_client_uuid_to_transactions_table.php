<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('client_uuid', 80)->nullable()->after('receipt_image');
            $table->unique(['couple_id', 'client_uuid']);
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['couple_id', 'client_uuid']);
            $table->dropColumn('client_uuid');
        });
    }
};
