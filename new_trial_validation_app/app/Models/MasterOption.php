<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `master_options` table — generic lookup lists keyed by `type`
 * (e.g. reviewer_department, role_category, validation_category). Minimal
 * stub for now, only what App\Models\User::reviewerDepartmentCodes() needs;
 * Fase 1 (Masters module) will flesh this out with full CRUD.
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by
 */
#[Fillable(['type', 'name', 'sort_order', 'is_active'])]
class MasterOption extends Model
{
    protected $table = 'master_options';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
