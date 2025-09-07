<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();

            // Fecha específica de la excepción
            $table->date('date');

            // Tipo de excepción: cancelación o adición extra
            $table->enum('type', ['cancel', 'extra'])->default('cancel');

            // Horario alternativo (solo se usa si es extra)
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->string('reason')->nullable(); // Vacaciones, congreso, emergencia, etc.
            $table->timestamps();
            $table->softDeletes();

            // índices/constraints útiles
            $table->index(['schedule_id', 'date']);
            $table->index(['date']);
            $table->unique(['schedule_id','date','type','start_time','end_time'], 'sched_exc_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
