<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_medicaloffice_id')->constrained('doctor_medicaloffices')->cascadeOnDelete();
            $table->timestamp('start_datetime')->index();
            $table->timestamp('end_datetime')->index();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['doctor_medicaloffice_id','start_datetime'], 'doctor_medicaloffice_start_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
