<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_offices', function (Blueprint $table) {
            // Update latitude and longitude to have better precision for coordinates
            $table->decimal('latitude', 8, 6)->nullable()->change();   // Range: -90.000000 to 90.000000
            $table->decimal('longitude', 9, 6)->nullable()->change();  // Range: -180.000000 to 180.000000
        });
    }

    public function down(): void
    {
        Schema::table('medical_offices', function (Blueprint $table) {
            // Revert to original precision
            $table->decimal('latitude', 10, 7)->nullable()->change();
            $table->decimal('longitude', 10, 7)->nullable()->change();
        });
    }
};
