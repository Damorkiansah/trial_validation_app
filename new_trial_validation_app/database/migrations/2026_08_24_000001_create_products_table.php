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
        // `products` is shared with the legacy app's MySQL database and
        // already exists there with its own schema — skip creating it when
        // the app is pointed at that shared DB. On a fresh (e.g. sqlite/test)
        // setup with no legacy data, create a table matching that same
        // legacy schema (see ../../../trial_validation_system.sql `products`).
        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('product_name', 200)->unique();
                $table->string('finish_good_code', 100);
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
        // Never drop the shared `products` table here — see note in up().
    }
};
