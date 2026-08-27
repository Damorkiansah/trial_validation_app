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
        // `audit_logs` is shared with the legacy app's MySQL database and
        // already exists there with its own schema — skip creating it when
        // the app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup, create a table matching that same legacy schema (see
        // ../../../database/trial_validation_system.sql `audit_logs`).
        if (! Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('trial_id')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('user_email', 150)->nullable();
                $table->string('action', 100);
                $table->longText('old_data')->nullable();
                $table->longText('new_data')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->dateTime('created_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `audit_logs` table here — see note in up().
    }
};
