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
        // `trial_attachment_files` is shared with the legacy app's MySQL
        // database and already exists there with its own schema — skip
        // creating it when the app is pointed at that shared DB. On a fresh
        // (e.g. sqlite/test) setup, create a table matching that same legacy
        // schema (see ../../../database/trial_validation_system.sql
        // `trial_attachment_files`). `deleted_at`/`deleted_by` exist in the
        // legacy schema but nothing — legacy or this app — ever sets them;
        // deletes are hard deletes (public/index.php:702).
        if (! Schema::hasTable('trial_attachment_files')) {
            Schema::create('trial_attachment_files', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trial_id');
                $table->string('category', 120);
                $table->string('file_name');
                $table->string('file_path');
                $table->string('uploaded_by', 150)->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('deleted_at')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `trial_attachment_files` table here — see note in up().
    }
};
