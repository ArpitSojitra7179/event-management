<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_name')->after('phone')->nullable();
            $table->string('country_code')->after('country_name')->nullable();
            $table->string('region_name')->after('country_code')->nullable();
            $table->string('region_code')->after('region_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country_name', 'country_code', 'region_name', 'region_code']);
        });
    }
};
