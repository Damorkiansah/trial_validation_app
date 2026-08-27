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
        // `trials_results` is shared with the legacy app's MySQL database and
        // already exists there with its own schema (composite primary key
        // trial_id+parameter_id, no surrogate id) — skip creating it when the
        // app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup, create a table matching that same legacy schema (see
        // ../../../trial_validation_system.sql `trials_results`).
        if (! Schema::hasTable('trials_results')) {
            Schema::create('trials_results', function (Blueprint $table) {
                $table->unsignedInteger('trial_id');
                $table->unsignedInteger('parameter_id');
                $table->text('result_value')->nullable();
                $table->string('decision', 20)->nullable();
                $table->text('remark')->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->primary(['trial_id', 'parameter_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `trials_results` table here — see note in up().
    }
};
