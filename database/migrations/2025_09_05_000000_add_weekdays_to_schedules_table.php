<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // store weekdays as CSV string (e.g. "1,2,3") for recurrence support
            $table->string('weekdays')->nullable()->after('doctor_medicaloffice_id')->index();
        });

        // backfill existing weekday values into weekdays column for compatibility
        try {
            DB::table('schedules')->whereNotNull('weekday')->whereRaw("COALESCE(weekdays,'') = ''")->update([
                'weekdays' => DB::raw("CAST(weekday AS CHAR)")
            ]);
        } catch (\Throwable $e) {
            // ignore on platforms where direct update might fail during migration run in tests
        }
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['weekdays']);
            $table->dropColumn('weekdays');
        });
    }
};
