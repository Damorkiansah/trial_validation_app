<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `validation_parameters` table — the per-product-type validation
 * parameter template list used when validating a trial. Port of the
 * /admin/parameters (/templates/parameters) block in the legacy app's
 * public/index.php.
 *
 * @property int $id
 * @property string $product_type
 * @property string $parameter_name
 * @property string|null $specification
 * @property int $sort_order
 * @property bool $is_active
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by
 */
#[Fillable(['product_type', 'parameter_name', 'specification', 'sort_order'])]
class ValidationParameter extends Model
{
    protected $table = 'validation_parameters';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
