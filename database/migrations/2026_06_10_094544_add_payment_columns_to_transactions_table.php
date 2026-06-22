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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('key')->after('amount')->nullable();
            $table->string('services')->after('key')->nullable();
            $table->string('gateway')->after('services')->nullable();
            $table->string('payment_link')->after('gateway')->nullable();
            $table->dateTime('paid_at')->after('payment_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['key', 'services', 'gateway', 'payment_link', 'paid_at']);
        });
    }
};
