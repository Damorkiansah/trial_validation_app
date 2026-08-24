<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $token
 * @property int $user_id
 * @property string $direction
 * @property Carbon $expires_at
 * @property Carbon|null $used_at
 * @property Carbon $created_at
 */
#[Fillable(['token', 'user_id', 'direction', 'expires_at', 'used_at'])]
class SsoTicket extends Model
{
    protected $table = 'sso_tickets';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }
}
