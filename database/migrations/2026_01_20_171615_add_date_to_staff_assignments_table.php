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
        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->date('assignment_date')->nullable()->after('station_id');
            $table->string('day_of_week', 10)->nullable()->change();

            $table->index('assignment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->dropIndex(['assignment_date']);
            $table->dropColumn('assignment_date');
            $table->string('day_of_week', 10)->nullable(false)->change();
        });
    }
};
