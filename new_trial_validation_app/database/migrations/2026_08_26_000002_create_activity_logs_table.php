<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // `activity_logs` is shared with the legacy app's MySQL database and
        // already exists there with its own schema (see
        // ../../../database/trial_validation_system.sql) — skip creating it
        // when the app is pointed at that shared DB. On a fresh (e.g.
        // sqlite/test) setup with no legacy data, create a table matching
        // that same legacy schema. Nothing in this app writes to this table
        // yet (logActivity() hasn't been ported — no trial workflow exists
        // here until Fase 3); this only supports the read/delete admin
        // screen (App\Http\Controllers\Admin\ActivityLogController).
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('user_name', 150)->nullable();
                $table->string('user_role', 80)->nullable();
                $table->string('action', 80);
                $table->string('module', 80);
                $table->string('record_id', 80)->nullable();
                $table->string('record_label', 255)->nullable();
                $table->longText('old_data')->nullable();
                $table->longText('new_data')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->dateTime('created_at')->useCurrent();

                $table->index('created_at', 'idx_activity_created');
                $table->index('user_id', 'idx_activity_user');
                $table->index('action', 'idx_activity_action');
                $table->index('module', 'idx_activity_module');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `activity_logs` table here — see note in up().
    }
};
