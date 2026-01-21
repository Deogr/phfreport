<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TicketItem;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->boolean('is_used')->default(false)->after('code');
        });

        // Migrate existing data
        TicketItem::where('status', 'used')->update(['is_used' => true]);

        Schema::table('ticket_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        Schema::table('ticket_items', function (Blueprint $table) {
            $table->string('status')->default('valid')->after('is_used');
        });

        TicketItem::where('is_used', true)->update(['status' => 'used']);

        Schema::table('ticket_items', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
};
