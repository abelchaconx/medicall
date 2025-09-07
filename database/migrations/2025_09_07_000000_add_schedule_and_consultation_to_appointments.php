<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->string('consultation_type')->nullable()->after('status');
            $table->text('consultation_notes')->nullable()->after('consultation_type');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'schedule_id')) {
                $table->dropForeign(['schedule_id']);
                $table->dropColumn('schedule_id');
            }
            if (Schema::hasColumn('appointments', 'consultation_type')) {
                $table->dropColumn('consultation_type');
            }
            if (Schema::hasColumn('appointments', 'consultation_notes')) {
                $table->dropColumn('consultation_notes');
            }
        });
    }
};
