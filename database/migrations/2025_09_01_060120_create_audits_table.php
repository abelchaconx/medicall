<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('table_name');
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('action');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('user_ip')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();
            $table->index(['table_name','record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
