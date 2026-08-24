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
        // `trial_edit_permissions` is shared with the legacy app's MySQL
        // database and already exists there (see
        // ../../../database/20260702_trial_edit_permissions.sql) — skip
        // creating it when the app is pointed at that shared DB. On a fresh
        // (e.g. sqlite/test) setup with no legacy data, create a table
        // matching that same legacy schema, so the Access Rights
        // draft-permission grant/revoke flow behaves identically everywhere.
        if (! Schema::hasTable('trial_edit_permissions')) {
            Schema::create('trial_edit_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('trial_id');
                $table->unsignedInteger('user_id');
                $table->boolean('can_edit')->default(true);
                $table->unsignedInteger('granted_by')->nullable();
                $table->dateTime('granted_at')->useCurrent();
                $table->unsignedInteger('revoked_by')->nullable();
                $table->dateTime('revoked_at')->nullable();
                $table->unique(['trial_id', 'user_id']);
                $table->index(['user_id', 'can_edit', 'revoked_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `trial_edit_permissions` table here — see note in up().
    }
};
