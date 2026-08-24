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
        // `trials_header` is shared with the legacy app's MySQL database and
        // already exists there with its own (much larger) schema — skip
        // creating it when the app is pointed at that shared DB. On a fresh
        // (e.g. sqlite/test) setup with no legacy data, create a table
        // matching just the columns App\Models\Trial actually maps onto (see
        // ../../../trial_validation_system.sql `trials_header`) — enough for
        // the Fase 0 RBAC port (TrialPolicy, Trial::scopeVisibleTo()) and the
        // Access Rights draft-permission grant/revoke flow to be exercised
        // against a real table in every environment. Fase 3 (Inti workflow
        // trial) will flesh this out with the rest of the workflow columns.
        if (! Schema::hasTable('trials_header')) {
            Schema::create('trials_header', function (Blueprint $table) {
                $table->id();
                $table->string('trial_code', 80);
                $table->unsignedInteger('product_id')->nullable();
                $table->string('product_name', 200);
                $table->string('finish_good_code', 100)->default('');
                $table->string('product_type', 100);
                $table->date('validation_date')->nullable();
                $table->string('validation_category', 120)->nullable();
                $table->string('risk_level', 20)->nullable();
                $table->string('validation_scope', 200)->nullable();
                $table->string('machine_used', 200)->nullable();
                $table->decimal('estimate_qty', 14, 2)->nullable();
                $table->string('batch_number', 200)->nullable();
                $table->string('bulk_code', 200)->nullable();
                $table->string('support_team', 200)->nullable();
                $table->string('initiated_person_team', 200)->nullable();
                $table->text('reason')->nullable();
                $table->text('bom')->nullable();
                $table->string('current_step', 80)->default('Validation');
                $table->string('progress_status', 80)->default('Draft');
                $table->string('pending_with', 200)->nullable();
                $table->string('final_decision', 80)->nullable();
                $table->integer('revision_no')->default(0);
                $table->string('approved_by', 150)->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->string('rejected_by', 150)->nullable();
                $table->dateTime('rejected_at')->nullable();
                $table->text('approval_comment')->nullable();
                $table->unsignedInteger('approver_user_id')->nullable();
                $table->string('created_by', 150)->nullable();
                $table->dateTime('updated_at')->nullable();
                $table->dateTime('created_at')->useCurrent();
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
        // Never drop the shared `trials_header` table here — see note in up().
    }
};
