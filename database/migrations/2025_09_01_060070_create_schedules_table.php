<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_place_id')->constrained('doctor_places')->cascadeOnDelete();
            $table->tinyInteger('weekday')->comment('0=Sunday..6=Saturday');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(30);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['doctor_place_id','weekday','start_time','end_time'], 'sched_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
