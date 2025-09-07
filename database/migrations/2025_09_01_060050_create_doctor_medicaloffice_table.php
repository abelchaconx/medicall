<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_medicaloffices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('medical_office_id')->constrained('medical_offices')->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['doctor_id','medical_office_id']);
        });
    }

    public function down(): void
    {
    Schema::dropIfExists('doctor_medicaloffices');
    }
};
