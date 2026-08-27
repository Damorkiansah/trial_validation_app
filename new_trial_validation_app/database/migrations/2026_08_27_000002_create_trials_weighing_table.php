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
        // `trials_weighing` is shared with the legacy app's MySQL database and
        // already exists there with its own schema — skip creating it when
        // the app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup, create a table matching that same legacy schema (see
        // ../../../database/trial_validation_system.sql `trials_weighing`).
        if (! Schema::hasTable('trials_weighing')) {
            Schema::create('trials_weighing', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('trial_id');
                $table->string('section', 30);
                $table->integer('item_no');
                $table->decimal('weight_value', 12, 3)->nullable();
                $table->boolean('is_skipped')->default(false);
                $table->timestamp('created_at')->useCurrent();
                $table->unique(['trial_id', 'section', 'item_no'], 'uq_weigh');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `trials_weighing` table here — see note in up().
    }
};
