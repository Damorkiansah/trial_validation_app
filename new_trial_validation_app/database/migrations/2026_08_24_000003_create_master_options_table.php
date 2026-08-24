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
        // `master_options` is shared with the legacy app's MySQL database
        // and already exists there with its own schema — skip creating it
        // when the app is pointed at that shared DB. On a fresh (e.g.
        // sqlite/test) setup with no legacy data, create a table matching
        // that same legacy schema (see
        // ../../../trial_validation_system.sql `master_options`,
        // ../../../database/20260508_admin_control_activity_pagination.sql
        // for the deleted_at/deleted_by columns added later). Used today by
        // App\Models\User's role/department helpers and the Parameters
        // module's product_type dropdown; full Masters CRUD is a later,
        // separate Fase 1 item.
        if (! Schema::hasTable('master_options')) {
            Schema::create('master_options', function (Blueprint $table) {
                $table->id();
                $table->string('type', 80);
                $table->string('name', 200);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->dateTime('deleted_at')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `master_options` table here — see note in up().
    }
};
