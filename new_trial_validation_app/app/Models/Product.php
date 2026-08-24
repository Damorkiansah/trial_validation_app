<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Legacy `products` table — the product/Finish Good Code template list used
 * when creating a trial. Port of the /admin/products (/templates/products)
 * block in the legacy app's public/index.php.
 *
 * @property int $id
 * @property string $product_name
 * @property string $finish_good_code
 * @property bool $is_active
 * @property Carbon|null $deleted_at
 * @property int|null $deleted_by
 */
#[Fillable(['product_name', 'finish_good_code'])]
class Product extends Model
{
    protected $table = 'products';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
