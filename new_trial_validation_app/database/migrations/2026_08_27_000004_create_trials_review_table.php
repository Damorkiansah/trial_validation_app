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
        // `trials_review` is shared with the legacy app's MySQL database and
        // already exists there with its own schema — skip creating it when
        // the app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup, create a table matching that same legacy schema (see
        // ../../../database/trial_validation_system.sql `trials_review`).
        if (! Schema::hasTable('trials_review')) {
            Schema::create('trials_review', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trial_id');
                $table->string('department', 50);
                $table->integer('review_round')->default(1);
                $table->string('status', 30)->default('Pending');
                $table->boolean('is_required')->default(true);
                $table->string('reviewer_name', 120)->nullable();
                $table->string('reviewer_email', 150)->nullable();
                $table->text('comment')->nullable();
                $table->dateTime('reviewed_at')->nullable();
                $table->unique(['trial_id', 'department', 'review_round'], 'uq_review_round');
                $table->index(['trial_id', 'review_round', 'status'], 'idx_review_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `trials_review` table here — see note in up().
    }
};
