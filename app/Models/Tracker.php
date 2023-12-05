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
 * @property int $order
 * @property string|null $unit
 * @property float $value_step
 * @property float $target_value
 * @property float $target_score
 * @property bool $single
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
        'order',
        'unit',
        'value_step',
        'target_value',
        'target_score',
        'single',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'value_step' => 'float',
        'target_value' => 'float',
        'target_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataPoints(): HasMany
    {
        return $this->hasMany(DataPoint::class)->orderByDesc('date');
    }

    public function getLastDataPoint(): DataPoint|Model|null
    {
        return $this->dataPoints()
            ->where('value', '!=', 0)
            ->first();
    }

    public function getScoreAt(Carbon $date): float
    {
        /** @var DataPoint $dataPoint */
        $dataPoint = $this->getDataPointAt($date);

        return $this->target_score * $dataPoint->value / $this->target_value;
    }

    public function getDataPointAt(Carbon $date): DataPoint|Model
    {
        return $this->dataPoints()
            ->firstOrCreate([
                'date' => $date,
            ])->refresh();
    }
}
