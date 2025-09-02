<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create pivot table role_user and migrate existing role_id values from users.
     */
    public function up()
    {
        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();

                $table->unique(['role_id','user_id']);
                $table->index('user_id');
                $table->index('role_id');
            });

            // Migrate existing role_id values from users table into pivot
            try {
                $rows = DB::table('users')->whereNotNull('role_id')->pluck('role_id','id');
                $now = date('Y-m-d H:i:s');
                $inserts = [];
                foreach ($rows as $userId => $roleId) {
                    $inserts[] = ['role_id' => $roleId, 'user_id' => $userId, 'created_at' => $now, 'updated_at' => $now];
                }
                if (! empty($inserts)) {
                    DB::table('role_user')->insert($inserts);
                }
            } catch (\Throwable $e) {
                // ignore on unsupported setups; admin can migrate manually
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('role_user');
    }
};
