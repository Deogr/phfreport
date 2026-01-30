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
        Schema::create('client_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // For registered clients
            $table->foreignId('therapist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('ticket_item_id')->nullable()->constrained('ticket_items')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->timestamp('appointment_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_assignments');
    }
};
