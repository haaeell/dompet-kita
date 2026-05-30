<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_locations', function (Blueprint $table) {
            $table->string('address_text')->nullable()->after('label');
            $table->string('road')->nullable()->after('address_text');
            $table->string('neighbourhood')->nullable()->after('road');
            $table->string('suburb')->nullable()->after('neighbourhood');
            $table->string('village')->nullable()->after('suburb');
            $table->string('district')->nullable()->after('village');
            $table->string('city')->nullable()->after('district');
            $table->string('state')->nullable()->after('city');
            $table->string('postcode', 20)->nullable()->after('state');
        });
    }

    public function down(): void
    {
        Schema::table('partner_locations', function (Blueprint $table) {
            $table->dropColumn([
                'address_text',
                'road',
                'neighbourhood',
                'suburb',
                'village',
                'district',
                'city',
                'state',
                'postcode',
            ]);
        });
    }
};
