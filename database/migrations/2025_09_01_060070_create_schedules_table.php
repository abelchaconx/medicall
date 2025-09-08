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
            $table->foreignId('doctor_medicaloffice_id')->constrained('doctor_medicaloffices')->cascadeOnDelete();
            $table->tinyInteger('weekday')->comment('0=Sunday..6=Saturday');
            $table->enum('turno', ['manana','tarde','noche'])->nullable()->comment('turno: manana, tarde, noche');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->default(30);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['doctor_medicaloffice_id','weekday','start_time','end_time'], 'sched_unique');
            $table->index(['weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
