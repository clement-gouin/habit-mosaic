<?php

namespace App\Models;

use Eloquent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Database\Factories\TrackerFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read Collection|DataPoint[] $dataPoints
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TrackerFactory factory($count = null, $state = [])
 * @method static Builder|Tracker newModelQuery()
 * @method static Builder|Tracker newQuery()
 * @method static Builder|Tracker query()
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
        'target_score',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataPoints(): HasMany
    {
        return $this->hasMany(DataPoint::class);
    }
}
