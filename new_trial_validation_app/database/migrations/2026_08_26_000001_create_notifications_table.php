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
        // `notifications` is shared with the legacy app's MySQL database and
        // already exists there with its own schema — skip creating it when
        // the app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup with no legacy data, create a table matching that same
        // legacy schema (see ../../../database/20260508_notifications.sql
        // plus the removed_by_user/removed_at columns added by
        // ../../../database/20260508_admin_control_activity_pagination.sql).
        // Only the columns the admin notifications screen reads/writes are
        // included — per-user read/removed state lives in
        // `notification_user_status`, out of scope until the per-user
        // notification bell is ported.
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->string('role_target', 50)->nullable();
                $table->string('department_target', 50)->nullable();
                $table->unsignedInteger('trial_id')->nullable();
                $table->string('title', 200);
                $table->text('message');
                $table->string('type', 40)->default('info');
                $table->boolean('is_read')->default(false);
                $table->dateTime('read_at')->nullable();
                $table->boolean('removed_by_user')->default(false);
                $table->dateTime('removed_at')->nullable();
                $table->dateTime('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `notifications` table here — see note in up().
    }
};
