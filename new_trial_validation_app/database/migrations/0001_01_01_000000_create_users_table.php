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
        // `users` is shared with the legacy app's MySQL database and already
        // exists there with its own schema — skip creating it when the app
        // is pointed at that shared DB. On a fresh (e.g. sqlite/test) setup
        // with no legacy data, create a table matching that same legacy
        // schema (see ../../../trial_validation_system.sql `users`), so
        // App\Models\User (which maps to that schema — role/department/
        // password_hash/is_active, no password/email_verified_at/
        // remember_token/updated_at) behaves identically in every environment.
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->string('email', 150)->unique();
                $table->string('password_hash');
                $table->string('role', 50);
                $table->string('department', 50)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('created_at')->useCurrent();
                $table->dateTime('deleted_at')->nullable();
                $table->unsignedInteger('deleted_by')->nullable();
            });
        }

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Never drop the shared `users` table here — see note in up().
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
