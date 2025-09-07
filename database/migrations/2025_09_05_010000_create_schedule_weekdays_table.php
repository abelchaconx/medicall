<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_weekdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->tinyInteger('weekday')->comment('1=Monday..7=Sunday');
            $table->timestamps();

            $table->unique(['schedule_id','weekday'], 'schedwd_unique');
            $table->index(['weekday']);
        });

        // Backfill from existing schedules table. Use both `weekdays` CSV and legacy `weekday`.
        try {
            $schedules = DB::table('schedules')->select('id','weekday','weekdays')->get();
            $inserts = [];
            foreach ($schedules as $s) {
                $vals = [];
                if (! empty($s->weekdays)) {
                    $parts = preg_split('/\s*,\s*/', trim($s->weekdays));
                    foreach ($parts as $p) {
                        if ($p === '') continue;
                        if (! is_numeric($p)) continue;
                        $n = (int) $p;
                        if ($n >= 0 && $n <= 7) $vals[] = $n;
                    }
                }
                if (empty($vals) && $s->weekday !== null) {
                    $n = (int) $s->weekday;
                    if ($n >= 0 && $n <= 7) $vals[] = $n;
                }

                foreach (array_unique($vals) as $w) {
                    $inserts[] = [
                        'schedule_id' => $s->id,
                        'weekday' => $w,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    // avoid growing memory too much
                    if (count($inserts) >= 500) {
                        DB::table('schedule_weekdays')->insertOrIgnore($inserts);
                        $inserts = [];
                    }
                }
            }
            if (! empty($inserts)) DB::table('schedule_weekdays')->insertOrIgnore($inserts);
        } catch (\Throwable $e) {
            // ignore backfill errors to keep migration safe in CI/dev where data may be absent
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_weekdays');
    }
};
