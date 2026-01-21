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
        Schema::table('tickets', function (Blueprint $table) {
            // Drop old columns
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['title', 'description', 'priority', 'assigned_to']);

            // Make user_id nullable for guests
            $table->unsignedBigInteger('user_id')->nullable()->change();

            // Add new columns
            $table->enum('type', ['single', 'bundle'])->default('single');
            $table->integer('quantity')->default(1);
            $table->decimal('price_per_ticket', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, momo, card
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');

            $table->dropColumn(['type', 'quantity', 'price_per_ticket', 'total_amount', 'payment_method', 'guest_name', 'guest_phone']);
        });
    }
};
