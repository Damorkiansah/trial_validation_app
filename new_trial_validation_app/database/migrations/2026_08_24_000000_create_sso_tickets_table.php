<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `sso_tickets` is shared with the legacy app's MySQL database, where
     * it's canonically created by `database/20260824_sso_tickets.sql`
     * (repo root) — this migration is a no-op there. It exists here only so
     * this app's own (ephemeral, sqlite) test database can self-provision
     * the table.
     */
    public function up(): void
    {
        if (Schema::hasTable('sso_tickets')) {
            return;
        }

        Schema::create('sso_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedInteger('user_id');
            $table->string('direction', 20);
            $table->dateTime('expires_at');
            $table->dateTime('used_at')->nullable();
            $table->dateTime('created_at')->useCurrent();

            $table->index(['user_id', 'direction']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop `sso_tickets` here — it's the shared, legacy-owned
        // table in any environment pointed at the real MySQL database.
    }
};
