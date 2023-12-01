<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Database\Factories\TrackerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Tracker
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $icon
 * @property string|null $unit
 * @property float $value_step
 * @property float $default_value
 * @property float $target_value
 * @property float $target_score
 * @property-read User $user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TrackerFactory factory($count = null, $state = [])
 * @method static Builder|UserToken newModelQuery()
 * @method static Builder|UserToken newQuery()
 * @method static Builder|UserToken query()
 * @mixin Eloquent
 */
class Tracker extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'icon',
        'unit',
        'value_step',
        'default_value',
        'target_value',
        'target_score'
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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
