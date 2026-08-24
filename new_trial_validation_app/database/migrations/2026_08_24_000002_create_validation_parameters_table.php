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
        // `validation_parameters` is shared with the legacy app's MySQL
        // database and already exists there with its own schema — skip
        // creating it when the app is pointed at that shared DB. On a fresh
        // (e.g. sqlite/test) setup with no legacy data, create a table
        // matching that same legacy schema (see
        // ../../../trial_validation_system.sql `validation_parameters`,
        // ../../../database/20260508_admin_control_activity_pagination.sql
        // for the deleted_at/deleted_by columns added later).
        if (! Schema::hasTable('validation_parameters')) {
            Schema::create('validation_parameters', function (Blueprint $table) {
                $table->id();
                $table->string('product_type', 100);
                $table->string('parameter_name', 200);
                $table->text('specification')->nullable();
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
        // Never drop the shared `validation_parameters` table here — see note in up().
    }
};
