<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->onDelete('set null');
            $table->foreignId('service_id')->nullable()->change(); // Make existing service_id nullable as we move to plans
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['subscription_plan_id']);
            $table->dropColumn('subscription_plan_id');
        });
    }
};
