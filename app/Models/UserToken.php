<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserToken
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property-read User $user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $last_used_at
 * @property Carbon $expires_at
 * @method static Builder|UserToken newModelQuery()
 * @method static Builder|UserToken newQuery()
 * @method static Builder|UserToken query()
 * @method static Builder|UserToken whereToken($value)
 * @mixin Eloquent
 */
class UserToken extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'expires_at',
        'last_used_at',
        'token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
