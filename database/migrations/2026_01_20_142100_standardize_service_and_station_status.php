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
        Schema::table('services', function (Blueprint $table) {
            $table->string('status')->default('active')->after('description'); // active, inactive
            $table->dropColumn('is_active');
        });

        // Stations already has status 'active','inactive' enum. Let's keep it but note it.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('description');
            $table->dropColumn('status');
        });
    }
};
